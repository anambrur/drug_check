<?php

namespace App\Services\Quest;

use App\Models\Admin\QuestOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class QuestOrderLifecycleService
{
    public function __construct(
        private readonly QuestEspClient $client,
        private readonly QuestXmlBuilder $xmlBuilder,
    ) {}

    public function getOrderDetails(QuestOrder $order): array
    {
        $this->assertQuestIdsPresent($order);

        return $this->client->getOrderDetails(
            $order->reference_test_id ?? '',
            $order->quest_order_id ?? ''
        );
    }

    public function getDocument(
        QuestOrder $order,
        string $docType,
        ?string $specimenId = null,
        ?string $accountNumber = null
    ): array {
        $this->assertQuestIdsPresent($order);

        $docXml = $this->xmlBuilder->buildGetDocumentXml(
            $order->quest_order_id ?? '',
            $order->reference_test_id ?? '',
            $docType,
            $specimenId,
            $accountNumber ?? $order->lab_account
        );

        return $this->client->getDocument($docXml);
    }

    public function updateOrder(QuestOrder $order, array $data): array
    {
        $this->assertQuestIdsPresent($order);

        $data['unit_codes'] = $this->xmlBuilder->normalizeUnitCodes($data['unit_codes'] ?? $order->unit_codes ?? []);
        $orderXml = $this->xmlBuilder->buildOrderXml($data, $order->client_reference_id);

        $result = $this->client->updateOrder(
            $order->reference_test_id ?? '',
            $order->quest_order_id ?? '',
            $orderXml
        );

        if ($result['status'] === 'SUCCESS') {
            $order->update($this->mapLocalFieldsFromData($data, $orderXml));
        }

        return $result;
    }

    public function cancelOrder(QuestOrder $order): array
    {
        $this->assertQuestIdsPresent($order);

        $result = $this->client->cancelOrder(
            $order->reference_test_id ?? '',
            $order->quest_order_id ?? ''
        );

        if ($result['status'] === 'SUCCESS') {
            $order->update([
                'order_status' => 'CANCELLED',
                'order_status_updated_at' => now(),
            ]);
        }

        return $result;
    }

    private function assertQuestIdsPresent(QuestOrder $order): void
    {
        if (empty($order->quest_order_id) || $order->create_response_status !== 'SUCCESS') {
            throw new \RuntimeException('This order does not have a valid Quest order ID.');
        }
    }

    private function mapLocalFieldsFromData(array $data, string $orderXml): array
    {
        return [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'primary_id' => $data['primary_id'],
            'primary_id_type' => $data['primary_id_type'] ?? null,
            'dob' => !empty($data['dob']) ? Carbon::parse($data['dob'])->toDateString() : null,
            'primary_phone' => $data['primary_phone'] ?? '',
            'secondary_phone' => $data['secondary_phone'] ?? null,
            'email' => $data['email'] ?? null,
            'zip_code' => $data['zip_code'] ?? null,
            'portfolio_id' => !empty($data['portfolio_id']) ? (int) $data['portfolio_id'] : null,
            'portfolio_name' => $data['portfolio_name'] ?? null,
            'unit_codes' => $this->xmlBuilder->normalizeUnitCodes($data['unit_codes'] ?? []),
            'dot_test' => $data['dot_test'],
            'testing_authority' => $data['testing_authority'] ?? null,
            'reason_for_test_id' => $data['reason_for_test_id'] ?? null,
            'physical_reason_for_test_id' => $data['physical_reason_for_test_id'] ?? null,
            'collection_site_id' => $data['collection_site_id'] ?? null,
            'observed_requested' => $data['observed_requested'] ?? 'N',
            'split_specimen_requested' => $data['split_specimen_requested'] ?? 'N',
            'order_comments' => $data['order_comments'] ?? null,
            'lab_account' => $data['lab_account'],
            'csl' => $data['csl'] ?? null,
            'contact_name' => $data['contact_name'] ?? null,
            'telephone_number' => $data['telephone_number'] ?? null,
            'end_datetime' => !empty($data['end_datetime']) ? Carbon::parse($data['end_datetime']) : null,
            'end_datetime_timezone_id' => $data['end_datetime_timezone_id'] ?? null,
            'request_xml' => $orderXml,
        ];
    }
}
