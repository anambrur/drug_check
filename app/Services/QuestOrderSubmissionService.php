<?php

namespace App\Services;

use App\Models\Admin\QuestOrder;
use App\Models\PortfolioTestApplication;
use App\Services\Quest\QuestEspClient;
use App\Services\Quest\QuestXmlBuilder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class QuestOrderSubmissionService
{
    public function __construct(
        private readonly PortfolioTestApplicationService $applicationService,
        private readonly QuestEspClient $questClient,
        private readonly QuestXmlBuilder $xmlBuilder,
    ) {}

    /**
     * Submit a paid application to Quest. Idempotent when already submitted.
     *
     * @return array{success: bool, order?: QuestOrder, error?: string, quest_order_id?: string, reference_test_id?: string}
     */
    public function submitFromApplication(PortfolioTestApplication $application): array
    {
        $lock = Cache::lock('quest-submit:' . $application->id, 120);

        try {
            $lock->block(30);

            $application->refresh();

            if ($application->quest_submission_status === 'submitted' && $application->quest_order_id) {
                $existing = QuestOrder::where('quest_order_id', $application->quest_order_id)->first();
                if ($existing) {
                    return [
                        'success' => true,
                        'order' => $existing,
                        'quest_order_id' => $existing->quest_order_id,
                        'reference_test_id' => $existing->reference_test_id,
                    ];
                }
            }

            if ($application->payment_status !== 'completed') {
                return ['success' => false, 'error' => 'Payment has not been completed.'];
            }

            try {
                $this->applicationService->verifyStripePaymentIntent($application);
            } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
                return ['success' => false, 'error' => $e->getMessage() ?: 'Payment verification failed.'];
            }

            $data = $this->applicationService->buildSubmitOrderData($application);

            try {
                $order = $this->submitOrderData(
                    $data,
                    $application->user_id !== null ? (int) $application->user_id : null
                );

                $application->update([
                    'quest_submission_status' => 'submitted',
                    'quest_submission_error' => null,
                    'quest_order_id' => $order->quest_order_id,
                    'status' => 'Quest Order Submitted',
                ]);

                return [
                    'success' => true,
                    'order' => $order,
                    'quest_order_id' => $order->quest_order_id,
                    'reference_test_id' => $order->reference_test_id,
                ];
            } catch (\Throwable $e) {
                Log::error('Quest auto-submit failed', [
                    'application_id' => $application->id,
                    'message' => $e->getMessage(),
                ]);

                $application->update([
                    'quest_submission_status' => 'failed',
                    'quest_submission_error' => $e->getMessage(),
                ]);

                return ['success' => false, 'error' => $e->getMessage()];
            }
        } finally {
            optional($lock)->release();
        }
    }

    /**
     * Submit validated order data to Quest and persist the order record.
     */
    public function submitOrderData(array $data, ?int $userId = null): QuestOrder
    {
        $data['unit_codes'] = $this->xmlBuilder->normalizeUnitCodes($data['unit_codes'] ?? []);

        $built = $this->xmlBuilder->buildOrderXml($data);
        $orderXml = $built['xml'];
        $clientReferenceId = $built['client_reference_id'];

        // Paid checkout must not be blocked by the circuit breaker — the customer
        // already paid and needs the order placed (or a clear Quest error).
        $result = $this->questClient->createOrder($orderXml, respectCircuitBreaker: false);

        Log::info('Quest order submission result', ['result' => $result]);

        if ($result['status'] !== 'SUCCESS') {
            throw new \RuntimeException(
                'Failed to create Quest order: ' . ($result['error']['detail'] ?? 'Unknown error.')
            );
        }

        // Persist a minimal recovery row first so an accepted Quest order is never invisible
        // if the full insert later fails.
        $recovery = QuestOrder::create([
            'user_id' => $userId ?? Auth::id(),
            'payment_intent_id' => $data['payment_intent_id'] ?? null,
            'quest_order_id' => $result['quest_order_id'] ?? null,
            'reference_test_id' => $result['reference_test_id'] ?? null,
            'client_reference_id' => $clientReferenceId,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'primary_id' => $data['primary_id'],
            'primary_phone' => $data['primary_phone'] ?? '',
            'dot_test' => $data['dot_test'],
            'lab_account' => $data['lab_account'],
            'unit_codes' => $data['unit_codes'],
            'request_xml' => $orderXml,
            'create_response_xml' => $result['_raw_response'] ?? null,
            'create_response_status' => $result['status'],
            'create_error' => isset($result['error']) ? json_encode($result['error']) : null,
        ]);

        try {
            return $this->hydrateQuestOrder($recovery, $data, $result, $orderXml, $clientReferenceId);
        } catch (\Throwable $e) {
            Log::error('Quest: failed to fully hydrate order after acceptance', [
                'error' => $e->getMessage(),
                'quest_order_id' => $recovery->quest_order_id,
                'recovery_id' => $recovery->id,
            ]);

            throw new \RuntimeException(
                'Your order was accepted by Quest but could not be fully saved. Please contact support with order ID '
                . ($recovery->quest_order_id ?? 'unknown') . '.',
                0,
                $e
            );
        }
    }

    private function hydrateQuestOrder(
        QuestOrder $order,
        array $data,
        array $apiResponse,
        string $orderXml,
        string $clientReferenceId
    ): QuestOrder {
        $order->update([
            'middle_name' => $this->nullIfEmpty($data['middle_name'] ?? null),
            'primary_id_type' => $this->nullIfEmpty($data['primary_id_type'] ?? null),
            'dob' => !empty($data['dob']) ? Carbon::parse($data['dob'])->toDateString() : null,
            'primary_phone' => $data['primary_phone'] ?? '',
            'secondary_phone' => $this->nullIfEmpty($data['secondary_phone'] ?? null),
            'email' => $this->nullIfEmpty($data['email'] ?? null),
            'zip_code' => $this->nullIfEmpty($data['zip_code'] ?? null),
            'portfolio_id' => !empty($data['portfolio_id']) ? (int) $data['portfolio_id'] : null,
            'portfolio_name' => $this->nullIfEmpty($data['portfolio_name'] ?? null),
            'unit_codes' => $this->xmlBuilder->normalizeUnitCodes($data['unit_codes'] ?? []),
            'testing_authority' => $this->nullIfEmpty($data['testing_authority'] ?? null),
            'reason_for_test_id' => !empty($data['reason_for_test_id']) ? (int) $data['reason_for_test_id'] : null,
            'physical_reason_for_test_id' => $this->nullIfEmpty($data['physical_reason_for_test_id'] ?? null),
            'collection_site_id' => $this->nullIfEmpty($data['collection_site_id'] ?? null),
            'observed_requested' => $this->nullIfEmpty($data['observed_requested'] ?? null) ?? 'N',
            'split_specimen_requested' => $this->nullIfEmpty($data['split_specimen_requested'] ?? null) ?? 'N',
            'order_comments' => $this->nullIfEmpty($data['order_comments'] ?? null),
            'lab_account' => $data['lab_account'],
            'csl' => $this->nullIfEmpty($data['csl'] ?? null),
            'contact_name' => $this->nullIfEmpty($data['contact_name'] ?? null),
            'telephone_number' => $this->nullIfEmpty($data['telephone_number'] ?? null),
            'end_datetime' => !empty($data['end_datetime']) ? Carbon::parse($data['end_datetime']) : null,
            'end_datetime_timezone_id' => !empty($data['end_datetime_timezone_id']) ? (int) $data['end_datetime_timezone_id'] : null,
            'response_url' => $this->nullIfEmpty($data['response_url'] ?? null),
            'request_xml' => $orderXml,
            'client_reference_id' => $clientReferenceId,
            'create_response_xml' => $apiResponse['_raw_response'] ?? null,
            'create_response_status' => $apiResponse['status'],
            'create_error' => isset($apiResponse['error']) ? json_encode($apiResponse['error']) : null,
        ]);

        return $order->fresh();
    }

    private function nullIfEmpty(mixed $value): mixed
    {
        if ($value === '' || $value === null) {
            return null;
        }

        return $value;
    }
}
