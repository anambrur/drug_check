<?php

namespace App\Services\Quest;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;

class QuestResponseParser
{
    private const MAX_RESPONSE_BYTES = 5 * 1024 * 1024;
    private const LIBXML_FLAGS = LIBXML_NONET | LIBXML_NOCDATA;

    public function parseQuestMethodResponse(string $xml, string $rawResponse = ''): array
    {
        $xml = $this->prepareXmlString($xml);

        $parsed = $this->loadXml($xml);
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

    public function parseSoapResponse(string $rawResponse): array
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

    public function parseDocumentResponse(string $rawResponse): array
    {
        $empty = ['status' => 'Error', 'error_detail' => '', 'doc_type' => '', 'doc_format' => '', 'doc_stream' => ''];

        if (empty(trim($rawResponse))) {
            return array_merge($empty, ['error_detail' => 'Empty response from Quest.']);
        }

        $inner = $this->extractInnerXml($this->cleanXml($rawResponse)) ?? $rawResponse;
        $xmlToParse = $this->prepareXmlString($inner);

        $xml = $this->loadXml($xmlToParse);
        if ($xml === false) {
            return array_merge($empty, ['error_detail' => 'Could not parse document response XML.']);
        }

        $status = (string) ($xml->ResponseStatusId ?? '');

        if ($status !== 'Success') {
            return array_merge($empty, [
                'status' => $status ?: 'Error',
                'error_detail' => (string) ($xml->ErrorDetail ?? 'Unknown document error.'),
                'doc_type' => (string) ($xml->DocType ?? ''),
                'doc_format' => (string) ($xml->DocFormat ?? ''),
            ]);
        }

        return [
            'status' => 'Success',
            'error_detail' => '',
            'doc_type' => (string) ($xml->DocType ?? ''),
            'doc_format' => (string) ($xml->DocFormat ?? ''),
            'doc_stream' => (string) ($xml->DocStream ?? ''),
        ];
    }

    public function parseInboundXml(string $rawBody): ?SimpleXMLElement
    {
        $cleaned = $this->decodeAndCleanInboundXml($rawBody);
        $inner = $this->tryExtractSoapInnerXml($cleaned);
        $xmlString = $inner ?? $cleaned;

        $xml = $this->loadXml($xmlString);

        return $xml instanceof SimpleXMLElement ? $xml : null;
    }

    public function extractOrderStatus(SimpleXMLElement $xml): ?array
    {
        $questOrderId = (string) ($xml->QuestOrderID ?? '');
        $statusId = (string) ($xml->OrderStatusID ?? '');

        if (empty($questOrderId) || empty($statusId)) {
            Log::error('Quest webhook: OrderStatus missing required fields', [
                'quest_order_id' => $questOrderId,
                'order_status_id' => $statusId,
            ]);

            return null;
        }

        return [
            'quest_order_id' => $questOrderId,
            'reference_test_id' => (string) ($xml->ReferenceTestID ?? ''),
            'client_reference_id' => (string) ($xml->ClientReferenceID ?? ''),
            'order_status_id' => $statusId,
            'screen_type' => strtolower((string) ($xml->ScreenType ?? 'drug')) ?: 'drug',
            'specimen_id' => (string) ($xml->SpecimenID ?? ''),
            'lab_accession_number' => (string) ($xml->LabAccessionNumber ?? ''),
            'collected_datetime' => $this->parseQuestDatetime($xml->CollectedDateTime ?? null),
            'order_status_datetime' => $this->parseQuestDatetime($xml->OrderStatusDateTime ?? null),
            'physical_data' => isset($xml->Physical) ? $this->xmlToArray($xml->Physical) : null,
        ];
    }

    public function extractOrderResult(SimpleXMLElement $xml): ?array
    {
        $questOrderId = (string) ($xml->QuestOrderID ?? '');
        $resultId = (string) ($xml->OrderResultID ?? '');

        if (empty($questOrderId) || empty($resultId)) {
            Log::error('Quest webhook: OrderResult missing required fields', [
                'quest_order_id' => $questOrderId,
                'order_result_id' => $resultId,
            ]);

            return null;
        }

        return [
            'quest_order_id' => $questOrderId,
            'reference_test_id' => (string) ($xml->ReferenceTestID ?? ''),
            'client_reference_id' => (string) ($xml->ClientReferenceID ?? ''),
            'order_result_id' => $resultId,
            'screen_type' => strtolower((string) ($xml->ScreenType ?? 'drug')) ?: 'drug',
            'specimen_id' => (string) ($xml->SpecimenID ?? ''),
            'lab_accession_number' => (string) ($xml->LabAccessionNumber ?? ''),
            'collected_datetime' => $this->parseQuestDatetime($xml->CollectedDateTime ?? null),
            'order_result_datetime' => $this->parseQuestDatetime($xml->OrderResultDateTime ?? null),
            'physical_data' => isset($xml->Physical) ? $this->xmlToArray($xml->Physical) : null,
        ];
    }

    public function extractInnerXml(string $soapResponse): ?string
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

        if (preg_match('/<Document[\s\S]*?<\/Document>/i', $current, $m)) {
            return $m[0];
        }

        return null;
    }

    public function decodeDocumentStream(string $stream): ?string
    {
        $decoded = base64_decode($stream, true);

        return $decoded === false ? null : $decoded;
    }

    private function loadXml(string $xml): SimpleXMLElement|false
    {
        libxml_use_internal_errors(true);
        $parsed = simplexml_load_string($xml, SimpleXMLElement::class, self::LIBXML_FLAGS);
        libxml_clear_errors();

        return $parsed;
    }

    private function prepareXmlString(string $xml): string
    {
        return html_entity_decode(trim(preg_replace('/<\?xml[^?]*\?>/', '', $xml)), ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    private function decodeAndCleanInboundXml(string $xml): string
    {
        $xml = html_entity_decode($xml, ENT_QUOTES | ENT_XML1, 'UTF-8');
        $xml = str_replace(['&#xD;', "\r"], '', $xml);
        $xml = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $xml);
        $xml = trim(preg_replace('/<\?xml[^?]*\?>/', '', $xml));

        return stripos($xml, '<?xml') === false
            ? '<?xml version="1.0" encoding="UTF-8"?>' . $xml
            : $xml;
    }

    private function tryExtractSoapInnerXml(string $xml): ?string
    {
        if (stripos($xml, 'Envelope') === false) {
            return null;
        }

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $loaded = $dom->loadXML($xml, self::LIBXML_FLAGS | LIBXML_NOWARNING | LIBXML_NOERROR);
        libxml_clear_errors();

        if (!$loaded) {
            return null;
        }

        $xpath = new \DOMXPath($dom);
        $nodes = $xpath->query('//*[local-name()="Body"]/*[1]');

        if ($nodes && $nodes->length > 0) {
            return $dom->saveXML($nodes->item(0));
        }

        return null;
    }

    private function isQuestPayload(string $xml): bool
    {
        $trimmed = trim(preg_replace('/<\?xml[^?]*\?>\s*/i', '', $xml));

        if (stripos($trimmed, '<s:Envelope') === 0 || stripos($trimmed, '<soap:Envelope') === 0 || stripos($trimmed, '<SOAP-ENV:') === 0) {
            return false;
        }

        return stripos($trimmed, '<QuestMethodResponse') === 0
            || stripos($trimmed, '<ResponseStatusID') !== false
            || stripos($trimmed, '<Document') === 0
            || stripos($trimmed, '<OrderStatusID') !== false
            || stripos($trimmed, '<OrderResultID') !== false;
    }

    private function extractOneLayer(string $current): ?string
    {
        $fixEncoding = fn (string $s) => preg_replace('/(<\?xml[^?]*encoding\s*=\s*["\'])utf-16(["\'])/i', '${1}utf-8${2}', $s);
        $addDecl = fn (string $s) => stripos($s, '<?xml') === false ? '<?xml version="1.0" encoding="UTF-8"?>' . $s : $s;

        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        libxml_use_internal_errors(true);
        $loaded = $dom->loadXML($fixEncoding($addDecl($current)), self::LIBXML_FLAGS | LIBXML_NOWARNING | LIBXML_NOERROR);
        libxml_clear_errors();

        if ($loaded) {
            $xpath = new \DOMXPath($dom);
            foreach (['soap', 's', 'SOAP-ENV'] as $prefix) {
                $xpath->registerNamespace($prefix, 'http://schemas.xmlsoap.org/soap/envelope/');
            }
            foreach ([
                '//*[local-name()="CreateOrderResult"]',
                '//*[local-name()="UpdateOrderResult"]',
                '//*[local-name()="CancelOrderResult"]',
                '//*[local-name()="GetOrderDetailsResult"]',
                '//*[local-name()="GetDocumentResult"]',
            ] as $query) {
                $nodes = $xpath->query($query);
                if ($nodes && $nodes->length > 0) {
                    return $nodes->item(0)->nodeValue;
                }
            }
        }

        libxml_use_internal_errors(true);
        $sxml = simplexml_load_string($fixEncoding($addDecl($current)), SimpleXMLElement::class, self::LIBXML_FLAGS);
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

        if (preg_match('/<(?:\w+:)?(?:CreateOrder|UpdateOrder|CancelOrder|GetOrderDetails|GetDocument)Result[^>]*>\s*(?:<!\[CDATA\[)?(.*?)(?:\]\]>)?\s*<\/(?:\w+:)?(?:CreateOrder|UpdateOrder|CancelOrder|GetOrderDetails|GetDocument)Result>/is', $current, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    private function parseQuestDatetime(mixed $value): ?Carbon
    {
        $raw = trim((string) ($value ?? ''));
        if (empty($raw)) {
            return null;
        }

        try {
            return Carbon::parse($raw);
        } catch (\Throwable) {
            return null;
        }
    }

    private function xmlToArray(SimpleXMLElement $xml): array
    {
        $result = [];
        foreach ($xml->children() as $key => $child) {
            $result[$key] = count($child->children()) > 0 ? $this->xmlToArray($child) : (string) $child;
        }

        return $result;
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
