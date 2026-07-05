<?php

namespace App\Services;

use App\Models\Admin\QuestOrder;
use App\Models\PortfolioTestApplication;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;

class QuestOrderSubmissionService
{
    private const CIRCUIT_BREAKER_KEY = 'quest_circuit_breaker';
    private const CIRCUIT_FAILURE_THRESHOLD = 3;
    private const CIRCUIT_RECOVERY_SECONDS = 60;
    private const MAX_RESPONSE_BYTES = 5 * 1024 * 1024;

    public function __construct(
        private readonly PortfolioTestApplicationService $applicationService
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
                $order = $this->submitOrderData($data, (int) $application->user_id);

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
        $orderXml = $this->buildOrderXml($data);
        $rawResponse = $this->callQuestCreateOrder($orderXml);
        $result = $this->parseSoapResponse($rawResponse);

        Log::info('Quest order submission result', ['result' => $result]);

        if ($result['status'] !== 'SUCCESS') {
            throw new \RuntimeException(
                'Failed to create Quest order: ' . ($result['error']['detail'] ?? 'Unknown error.')
            );
        }

        return $this->storeQuestOrder($data, $result, $orderXml, $userId ?? Auth::id());
    }

    private function buildOrderXml(array $data): string
    {
        $xml = new SimpleXMLElement('<Order/>');

        $eventInfo = $xml->addChild('EventInfo');

        if (!empty($data['collection_site_id'])) {
            $eventInfo->addChild('CollectionSiteID', $data['collection_site_id']);
        }

        if (!empty($data['email'])) {
            $eventInfo->addChild('EmailAuthorizationAddresses')
                ->addChild('EmailAddress', $data['email']);
        }

        if (!empty($data['end_datetime'])) {
            try {
                $eventInfo->addChild(
                    'EndDateTime',
                    Carbon::createFromFormat('Y-m-d\TH:i', $data['end_datetime'])->format('m/d/Y H:i:s')
                );
            } catch (\Throwable) {
                Log::warning('Quest: could not reformat end_datetime', ['value' => $data['end_datetime']]);
            }
            if (!empty($data['end_datetime_timezone_id'])) {
                $eventInfo->addChild('EndDateTimeTimeZoneID', $data['end_datetime_timezone_id']);
            }
        }

        $donorInfo = $xml->addChild('DonorInfo');
        $donorInfo->addChild('FirstName', $data['first_name']);
        $donorInfo->addChild('LastName', $data['last_name']);

        if (!empty($data['middle_name'])) {
            $donorInfo->addChild('MiddleName', $data['middle_name']);
        }

        $donorInfo->addChild('PrimaryID', $data['primary_id']);

        if (!empty($data['primary_id_type'])) {
            $donorInfo->addChild('PrimaryIDType', $data['primary_id_type']);
        }

        if (!empty($data['dob'])) {
            try {
                $donorInfo->addChild('DOB', Carbon::parse($data['dob'])->format('m/d/Y'));
            } catch (\Throwable) {
                Log::warning('Quest: could not reformat dob', ['value' => $data['dob']]);
            }
        }

        $donorInfo->addChild('PrimaryPhone', $this->digitsOnly($data['primary_phone'] ?? ''));

        if (!empty($data['secondary_phone'])) {
            $donorInfo->addChild('SecondaryPhone', $this->digitsOnly($data['secondary_phone']));
        }

        if (!empty($data['zip_code'])) {
            $donorInfo->addChild('PostalAddress')->addChild('ZipCode', $data['zip_code']);
        }

        $clientInfo = $xml->addChild('ClientInfo');

        if (!empty($data['contact_name'])) {
            $clientInfo->addChild('ContactName', $data['contact_name']);
        }

        if (!empty($data['telephone_number'])) {
            $clientInfo->addChild('TelephoneNumber', substr($this->digitsOnly($data['telephone_number']), 0, 10));
        }

        $clientInfo->addChild('LabAccount', $data['lab_account']);

        if (!empty($data['csl'])) {
            $clientInfo->addChild('CSL', $data['csl']);
        }

        $testInfo = $xml->addChild('TestInfo');
        $testInfo->addChild('ClientReferenceID', $this->generateClientReferenceId());
        $testInfo->addChild('DOTTest', $data['dot_test']);

        if ($data['dot_test'] === 'T' && !empty($data['testing_authority'])) {
            $testInfo->addChild('TestingAuthority', $data['testing_authority']);
        }

        if (!empty($data['reason_for_test_id'])) {
            $testInfo->addChild('ReasonForTestID', $data['reason_for_test_id']);
        }

        if (!empty($data['physical_reason_for_test_id'])) {
            $testInfo->addChild('PhysicalReasonForTestID', $data['physical_reason_for_test_id']);
        }

        if (!empty($data['observed_requested'])) {
            $testInfo->addChild('ObservedRequested', $data['observed_requested']);
        }

        if (!empty($data['split_specimen_requested'])) {
            $testInfo->addChild('SplitSpecimenRequested', $data['split_specimen_requested']);
        }

        if (!empty($data['order_comments'])) {
            $testInfo->addChild('OrderComments', $data['order_comments']);
        }

        $testInfo->addChild('Screenings')
            ->addChild('UnitCodes')
            ->addChild('UnitCode', $data['unit_codes']);

        if (!empty($data['response_url'])) {
            $xml->addChild('ClientCustom')->addChild('ResponseURL', $data['response_url']);
        }

        return trim(preg_replace('/<\?xml[^?]*\?>/', '', $xml->asXML()));
    }

    private function callQuestCreateOrder(string $orderXml): string
    {
        return $this->sendCurlRequest(
            $this->buildSoapEnvelope($orderXml),
            $this->getSoapAction('CreateOrder')
        );
    }

    private function sendCurlRequest(string $soapBody, string $soapAction): string
    {
        $this->checkCircuitBreaker();

        $sslVerify = config('services.quest.ssl.verify_peer', true);
        $caBundle = config('services.quest.ssl.ca_bundle');

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->getApiUrl(),
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $soapBody,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => config('services.quest.timeouts.request', 60),
            CURLOPT_CONNECTTIMEOUT => config('services.quest.timeouts.connect', 60),
            CURLOPT_SSL_VERIFYPEER => $sslVerify,
            CURLOPT_SSL_VERIFYHOST => $sslVerify ? 2 : 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_ENCODING => '',
            CURLOPT_HTTPHEADER => [
                'Content-Type: text/xml; charset=utf-8',
                'SOAPAction: ' . $soapAction,
                'Content-Length: ' . strlen($soapBody),
                'Connection: Keep-Alive',
            ],
        ]);

        if ($sslVerify && $caBundle) {
            curl_setopt($ch, CURLOPT_CAINFO, $caBundle);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            $this->recordCircuitFailure();
            throw new \RuntimeException(match (true) {
                str_contains($error, 'timed out') => 'Quest API timeout. Please try again.',
                str_contains($error, 'Could not resolve host') => 'Cannot reach the Quest Diagnostics server.',
                default => 'Quest API connection failed: ' . $error,
            });
        }

        if ($httpCode !== 200) {
            $this->recordCircuitFailure();
            Log::error('Quest API non-200', ['action' => $soapAction, 'status' => $httpCode, 'body' => substr($response, 0, 400)]);
            throw new \RuntimeException("Quest Diagnostics returned HTTP {$httpCode}.");
        }

        $this->resetCircuitBreaker();

        return $response;
    }

    private function buildSoapEnvelope(string $orderXml): string
    {
        return '<?xml version="1.0" encoding="utf-8"?>'
            . '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"'
            . ' xmlns:wss="http://wssim.labone.com/">'
            . '<soap:Body>'
            . '<wss:CreateOrder>'
            . '<wss:username>' . $this->xmlEscape($this->getUsername()) . '</wss:username>'
            . '<wss:password>' . $this->xmlEscape($this->getPassword()) . '</wss:password>'
            . '<wss:orderXml><![CDATA[' . $orderXml . ']]></wss:orderXml>'
            . '</wss:CreateOrder>'
            . '</soap:Body>'
            . '</soap:Envelope>';
    }

    private function parseSoapResponse(string $rawResponse): array
    {
        if (strlen($rawResponse) > self::MAX_RESPONSE_BYTES) {
            return $this->buildFailureResult('RESPONSE_TOO_LARGE', 'Response exceeded maximum allowed size.', $rawResponse);
        }

        $innerXml = $this->extractInnerXml($this->cleanXml($rawResponse));

        if ($innerXml === null) {
            Log::error('Quest: could not extract inner XML', ['snippet' => substr($rawResponse, 0, 400)]);

            return $this->buildFailureResult('SOAP_EXTRACTION_FAILED', 'Could not extract response body.', $rawResponse);
        }

        return $this->parseQuestMethodResponse($innerXml, $rawResponse);
    }

    private function parseQuestMethodResponse(string $xml, string $rawResponse = ''): array
    {
        $xml = html_entity_decode(trim(preg_replace('/<\?xml[^?]*\?>/', '', $xml)), ENT_QUOTES | ENT_XML1, 'UTF-8');

        libxml_use_internal_errors(true);
        $parsed = simplexml_load_string($xml);
        libxml_clear_errors();

        if ($parsed === false) {
            return $this->buildFailureResult('XML_PARSE_ERROR', 'Failed to parse Quest response XML.', $rawResponse);
        }

        $result = [
            'method_id' => (string) ($parsed->MethodID ?? ''),
            'client_reference_id' => (string) ($parsed->ClientReferenceID ?? ''),
            'reference_test_id' => (string) ($parsed->ReferenceTestID ?? ''),
            'quest_order_id' => (string) ($parsed->QuestOrderID ?? '0'),
            'status' => (string) ($parsed->ResponseStatusID ?? 'FAILURE'),
            'display_url' => (string) ($parsed->DisplayURL ?? ''),
            'error' => null,
            '_raw_response' => $rawResponse,
        ];

        if ($result['status'] === 'FAILURE' && isset($parsed->Errors->Error)) {
            $error = $parsed->Errors->Error[0];
            $result['error'] = [
                'id' => (string) ($error->ErrorID ?? 'UNKNOWN'),
                'detail' => (string) ($error->ErrorDetail ?? 'An unknown error occurred.'),
            ];
        }

        return $result;
    }

    private function extractInnerXml(string $soapResponse): ?string
    {
        $current = $soapResponse;

        for ($depth = 0; $depth < 5; $depth++) {
            $current = str_replace(['&#xD;', "\r"], '', $current);
            $next = $this->extractOneLayer($current);
            if ($next === null) {
                break;
            }

            $next = trim(html_entity_decode(str_replace(['&#xD;', "\r"], '', $next), ENT_QUOTES | ENT_XML1, 'UTF-8'));
            if ($next === $current) {
                break;
            }

            $current = $next;
            if ($this->isQuestPayload($current)) {
                return $current;
            }
        }

        if ($this->isQuestPayload($current)) {
            return $current;
        }

        if (preg_match('/<QuestMethodResponse[\s\S]*?<\/QuestMethodResponse>/i', $current, $m)) {
            return $m[0];
        }

        return null;
    }

    private function isQuestPayload(string $xml): bool
    {
        $trimmed = trim(preg_replace('/<\?xml[^?]*\?>\s*/i', '', $xml));

        if (stripos($trimmed, '<s:Envelope') === 0 || stripos($trimmed, '<soap:Envelope') === 0 || stripos($trimmed, '<SOAP-ENV:') === 0) {
            return false;
        }

        return stripos($trimmed, '<QuestMethodResponse') === 0 || stripos($trimmed, '<ResponseStatusID') !== false;
    }

    private function extractOneLayer(string $current): ?string
    {
        $fixEncoding = fn (string $s) => preg_replace('/(<\?xml[^?]*encoding\s*=\s*["\'])utf-16(["\'])/i', '${1}utf-8${2}', $s);
        $addDecl = fn (string $s) => stripos($s, '<?xml') === false ? '<?xml version="1.0" encoding="UTF-8"?>' . $s : $s;

        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        libxml_use_internal_errors(true);
        $loaded = $dom->loadXML($fixEncoding($addDecl($current)), LIBXML_NOWARNING | LIBXML_NOERROR);
        libxml_clear_errors();

        if ($loaded) {
            $xpath = new \DOMXPath($dom);
            foreach (['soap', 's', 'SOAP-ENV'] as $prefix) {
                $xpath->registerNamespace($prefix, 'http://schemas.xmlsoap.org/soap/envelope/');
            }
            foreach (['//*[local-name()="CreateOrderResult"]', '//*[local-name()="GetOrderDetailsResult"]', '//*[local-name()="GetDocumentResult"]'] as $query) {
                $nodes = $xpath->query($query);
                if ($nodes && $nodes->length > 0) {
                    return $nodes->item(0)->nodeValue;
                }
            }
        }

        libxml_use_internal_errors(true);
        $sxml = simplexml_load_string($fixEncoding($addDecl($current)));
        libxml_clear_errors();

        if ($sxml !== false) {
            foreach (['http://schemas.xmlsoap.org/soap/envelope/', 'http://tempuri.org/', 'http://wssim.labone.com/'] as $ns) {
                try {
                    $body = $sxml->children($ns)->Body ?? null;
                } catch (\Throwable) {
                    $body = null;
                }
                if (!$body) {
                    continue;
                }
                foreach ($body->children() as $responseNode) {
                    foreach ($responseNode->children() as $resultNode) {
                        if (str_ends_with($resultNode->getName(), 'Result')) {
                            return (string) $resultNode;
                        }
                    }
                }
            }
        }

        if (preg_match('/<(?:\w+:)?(?:CreateOrder|GetOrderDetails|GetDocument)Result[^>]*>\s*(?:<!\[CDATA\[)?(.*?)(?:\]\]>)?\s*<\/(?:\w+:)?(?:CreateOrder|GetOrderDetails|GetDocument)Result>/is', $current, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    private function storeQuestOrder(array $data, array $apiResponse, string $orderXml, ?int $userId): QuestOrder
    {
        try {
            return QuestOrder::create([
                'user_id' => $userId,
                'payment_intent_id' => $data['payment_intent_id'],
                'quest_order_id' => $apiResponse['quest_order_id'] ?? null,
                'reference_test_id' => $apiResponse['reference_test_id'] ?? null,
                'client_reference_id' => $apiResponse['client_reference_id'] ?? $this->generateClientReferenceId(),
                'order_status' => null,
                'order_result' => null,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'middle_name' => $this->nullIfEmpty($data['middle_name'] ?? null),
                'primary_id' => $data['primary_id'],
                'primary_id_type' => $this->nullIfEmpty($data['primary_id_type'] ?? null),
                'dob' => !empty($data['dob']) ? Carbon::parse($data['dob'])->toDateString() : null,
                'primary_phone' => $data['primary_phone'] ?? '',
                'secondary_phone' => $this->nullIfEmpty($data['secondary_phone'] ?? null),
                'email' => $this->nullIfEmpty($data['email'] ?? null),
                'zip_code' => $this->nullIfEmpty($data['zip_code'] ?? null),
                'portfolio_id' => !empty($data['portfolio_id']) ? (int) $data['portfolio_id'] : null,
                'unit_codes' => json_encode($data['unit_codes']),
                'dot_test' => $data['dot_test'],
                'testing_authority' => $this->nullIfEmpty($data['testing_authority'] ?? null),
                'reason_for_test_id' => !empty($data['reason_for_test_id']) ? (int) $data['reason_for_test_id'] : null,
                'physical_reason_for_test_id' => $this->nullIfEmpty($data['physical_reason_for_test_id'] ?? null),
                'collection_site_id' => $this->nullIfEmpty($data['collection_site_id'] ?? null),
                'observed_requested' => $this->nullIfEmpty($data['observed_requested'] ?? null) ?? 'N',
                'split_specimen_requested' => $this->nullIfEmpty($data['split_specimen_requested'] ?? null) ?? 'N',
                'order_comments' => $this->nullIfEmpty($data['order_comments'] ?? null),
                'lab_account' => app()->isProduction() ? $data['lab_account'] : config('services.quest.lab_account'),
                'csl' => $this->nullIfEmpty($data['csl'] ?? null),
                'contact_name' => $this->nullIfEmpty($data['contact_name'] ?? null),
                'telephone_number' => $this->nullIfEmpty($data['telephone_number'] ?? null),
                'end_datetime' => !empty($data['end_datetime']) ? Carbon::parse($data['end_datetime']) : null,
                'end_datetime_timezone_id' => !empty($data['end_datetime_timezone_id']) ? (int) $data['end_datetime_timezone_id'] : null,
                'response_url' => $this->nullIfEmpty($data['response_url'] ?? null),
                'request_xml' => $orderXml,
                'create_response_xml' => $apiResponse['_raw_response'] ?? null,
                'create_response_status' => $apiResponse['status'],
                'create_error' => isset($apiResponse['error']) ? json_encode($apiResponse['error']) : null,
            ]);
        } catch (\Throwable $e) {
            Log::error('Quest: failed to store order', ['error' => $e->getMessage()]);
            throw new \RuntimeException('Your order was accepted by Quest but could not be saved. Please contact support.', 0, $e);
        }
    }

    private function getApiUrl(): string
    {
        return app()->isProduction()
            ? config('services.quest.urls.production', 'https://qcs.questdiagnostics.com/services/ESPService.asmx')
            : config('services.quest.urls.staging', 'https://qcs-uat.questdiagnostics.com/services/ESPService.asmx');
    }

    private function getUsername(): string
    {
        return config('services.quest.username')
            ?? throw new \RuntimeException('QUEST_USERNAME is not configured.');
    }

    private function getPassword(): string
    {
        return config('services.quest.password')
            ?? throw new \RuntimeException('QUEST_PASSWORD is not configured.');
    }

    private function getNamespace(): string
    {
        return 'http://wssim.labone.com';
    }

    private function getSoapAction(string $action): string
    {
        return '"' . $this->getNamespace() . '/' . $action . '"';
    }

    private function checkCircuitBreaker(): void
    {
        $state = $this->getCircuitState();
        if (!$state['open']) {
            return;
        }
        if ((time() - $state['last_failure']) < self::CIRCUIT_RECOVERY_SECONDS) {
            throw new \RuntimeException('Quest Diagnostics is temporarily unavailable. Please try again in a moment.');
        }
        $this->resetCircuitBreaker();
    }

    private function recordCircuitFailure(): void
    {
        $state = $this->getCircuitState();
        $state['failures']++;
        $state['last_failure'] = time();
        if ($state['failures'] >= self::CIRCUIT_FAILURE_THRESHOLD) {
            $state['open'] = true;
            Log::error('Quest circuit breaker opened', ['failures' => $state['failures']]);
        }
        $this->saveCircuitState($state);
    }

    private function resetCircuitBreaker(): void
    {
        $this->saveCircuitState(['open' => false, 'failures' => 0, 'last_failure' => 0]);
    }

    private function getCircuitState(): array
    {
        return Cache::get(self::CIRCUIT_BREAKER_KEY, ['open' => false, 'failures' => 0, 'last_failure' => 0]);
    }

    private function saveCircuitState(array $state): void
    {
        Cache::put(self::CIRCUIT_BREAKER_KEY, $state, now()->addHour());
    }

    private function generateClientReferenceId(): string
    {
        return 'ORDER_' . now()->format('Ymd_His') . '_' . random_int(1000, 9999);
    }

    private function nullIfEmpty(mixed $value): mixed
    {
        if ($value === '' || $value === null) {
            return null;
        }

        return $value;
    }

    private function digitsOnly(?string $value): string
    {
        $value = $value ?? '';
        $result = preg_replace('/[^0-9]/', '', $value);

        return $result === null ? '' : $result;
    }

    private function xmlEscape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1, 'UTF-8');
    }

    private function cleanXml(string $xml): string
    {
        return preg_replace('/\s+/', ' ', preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $xml));
    }

    private function buildFailureResult(string $errorId, string $detail, string $rawResponse): array
    {
        return [
            'method_id' => null,
            'client_reference_id' => null,
            'reference_test_id' => null,
            'quest_order_id' => '0',
            'status' => 'FAILURE',
            'display_url' => null,
            'error' => ['id' => $errorId, 'detail' => $detail],
            '_raw_response' => substr($rawResponse, 0, 2000),
        ];
    }
}
