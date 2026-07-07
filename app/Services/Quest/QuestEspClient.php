<?php

namespace App\Services\Quest;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class QuestEspClient
{
    private const CIRCUIT_BREAKER_KEY = 'quest_circuit_breaker';
    private const CIRCUIT_FAILURE_THRESHOLD = 3;
    private const CIRCUIT_RECOVERY_SECONDS = 60;
    private const MAX_RESPONSE_BYTES = 5 * 1024 * 1024;

    public function __construct(
        private readonly QuestResponseParser $parser
    ) {}

    public function createOrder(string $orderXml): array
    {
        $soapBody = $this->buildSoapEnvelope('CreateOrder', [
            'orderXml' => $orderXml,
        ]);

        $rawResponse = $this->sendCurlRequest($soapBody, $this->getSoapAction('CreateOrder'));

        return $this->parser->parseSoapResponse($rawResponse);
    }

    public function updateOrder(string $referenceTestId, string $questOrderId, string $orderXml): array
    {
        $soapBody = $this->buildSoapEnvelope('UpdateOrder', [
            'referenceTestId' => $referenceTestId,
            'questOrderId' => $questOrderId,
            'orderXml' => $orderXml,
        ]);

        $rawResponse = $this->sendCurlRequest($soapBody, $this->getSoapAction('UpdateOrder'));

        return $this->parser->parseSoapResponse($rawResponse);
    }

    public function cancelOrder(string $referenceTestId, string $questOrderId): array
    {
        $soapBody = $this->buildSoapEnvelope('CancelOrder', [
            'referenceTestId' => $referenceTestId,
            'questOrderId' => $questOrderId,
        ]);

        $rawResponse = $this->sendCurlRequest($soapBody, $this->getSoapAction('CancelOrder'));

        return $this->parser->parseSoapResponse($rawResponse);
    }

    public function getOrderDetails(string $referenceTestId, string $questOrderId): array
    {
        $soapBody = $this->buildSoapEnvelope('GetOrderDetails', [
            'referenceTestId' => $referenceTestId,
            'questOrderId' => $questOrderId,
        ]);

        $rawResponse = $this->sendCurlRequest($soapBody, $this->getSoapAction('GetOrderDetails'));

        return $this->parser->parseSoapResponse($rawResponse);
    }

    public function getDocument(string $docXml): array
    {
        $soapBody = $this->buildSoapEnvelope('GetDocument', [
            'DocXml' => $docXml,
        ]);

        $rawResponse = $this->sendCurlRequest($soapBody, $this->getSoapAction('GetDocument'));

        return $this->parser->parseDocumentResponse($rawResponse);
    }

    private function buildSoapEnvelope(string $method, array $params): string
    {
        $body = '<wss:' . $method . '>'
            . '<wss:username>' . $this->xmlEscape($this->getUsername()) . '</wss:username>'
            . '<wss:password>' . $this->xmlEscape($this->getPassword()) . '</wss:password>';

        foreach ($params as $key => $value) {
            if ($key === 'orderXml' || $key === 'DocXml') {
                $body .= '<wss:' . $key . '><![CDATA[' . $value . ']]></wss:' . $key . '>';
            } else {
                $body .= '<wss:' . $key . '>' . $this->xmlEscape((string) $value) . '</wss:' . $key . '>';
            }
        }

        $body .= '</wss:' . $method . '>';

        return '<?xml version="1.0" encoding="utf-8"?>'
            . '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"'
            . ' xmlns:wss="http://wssim.labone.com/">'
            . '<soap:Body>'
            . $body
            . '</soap:Body>'
            . '</soap:Envelope>';
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
            Log::error('Quest API non-200', [
                'action' => $soapAction,
                'status' => $httpCode,
                'body' => substr((string) $response, 0, 400),
            ]);
            throw new \RuntimeException("Quest Diagnostics returned HTTP {$httpCode}.");
        }

        if (strlen((string) $response) > self::MAX_RESPONSE_BYTES) {
            throw new \RuntimeException('Quest response exceeded maximum allowed size.');
        }

        $this->resetCircuitBreaker();

        return (string) $response;
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

    private function xmlEscape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1, 'UTF-8');
    }
}
