<?php

namespace App\Services\Quest;

use App\Models\Admin\QuestOrder;
use Illuminate\Support\Facades\Log;

class QuestWebhookService
{
    public function __construct(
        private readonly QuestResponseParser $parser,
        private readonly QuestOrderScreenService $screenService,
    ) {}

    public function handle(string $rawBody, string $clientIp): string
    {
        $maxBytes = (int) config('services.quest.webhook.max_body_bytes', 1048576);
        if (strlen($rawBody) > $maxBytes) {
            Log::warning('Quest webhook: body too large', ['ip' => $clientIp, 'bytes' => strlen($rawBody)]);

            return 'PAYLOAD_TOO_LARGE';
        }

        $xml = $this->parser->parseInboundXml($rawBody);
        if ($xml === null) {
            Log::error('Quest webhook: could not parse inbound XML', [
                'ip' => $clientIp,
                'snippet' => $this->logSnippet($rawBody),
            ]);

            return 'PARSE_ERROR';
        }

        if (isset($xml->OrderStatusID)) {
            return $this->handleStatus($xml, $rawBody, $clientIp);
        }

        if (isset($xml->OrderResultID)) {
            return $this->handleResult($xml, $rawBody, $clientIp);
        }

        Log::warning('Quest webhook: unrecognised payload', [
            'ip' => $clientIp,
            'snippet' => $this->logSnippet($rawBody),
        ]);

        return 'UNKNOWN_PAYLOAD';
    }

    private function handleStatus(\SimpleXMLElement $xml, string $rawBody, string $clientIp): string
    {
        $status = $this->parser->extractOrderStatus($xml);
        if ($status === null) {
            return 'INVALID_STATUS';
        }

        $this->logWebhookEvent('OrderStatus', $status, $clientIp, strlen($rawBody));

        $order = QuestOrder::where('quest_order_id', $status['quest_order_id'])->first();
        if (!$order) {
            Log::warning('Quest webhook: OrderStatus for unknown quest_order_id', [
                'quest_order_id' => $status['quest_order_id'],
                'status_id' => $status['order_status_id'],
            ]);

            return 'SUCCESS';
        }

        try {
            $this->screenService->applyStatus($order, $status, $rawBody);
        } catch (\Throwable $e) {
            Log::error('Quest webhook: failed to apply OrderStatus', [
                'error' => $e->getMessage(),
                'quest_order_id' => $status['quest_order_id'],
            ]);
        }

        return 'SUCCESS';
    }

    private function handleResult(\SimpleXMLElement $xml, string $rawBody, string $clientIp): string
    {
        $result = $this->parser->extractOrderResult($xml);
        if ($result === null) {
            return 'INVALID_RESULT';
        }

        $this->logWebhookEvent('OrderResult', $result, $clientIp, strlen($rawBody));

        $order = QuestOrder::where('quest_order_id', $result['quest_order_id'])->first();
        if (!$order) {
            Log::warning('Quest webhook: OrderResult for unknown quest_order_id', [
                'quest_order_id' => $result['quest_order_id'],
                'result_id' => $result['order_result_id'],
            ]);

            return 'SUCCESS';
        }

        try {
            $this->screenService->applyResult($order, $result, $rawBody);
        } catch (\Throwable $e) {
            Log::error('Quest webhook: failed to apply OrderResult', [
                'error' => $e->getMessage(),
                'quest_order_id' => $result['quest_order_id'],
            ]);
        }

        return 'SUCCESS';
    }

    private function logWebhookEvent(string $type, array $payload, string $clientIp, int $bytes): void
    {
        $context = [
            'ip' => $clientIp,
            'bytes' => $bytes,
            'quest_order_id' => $payload['quest_order_id'] ?? null,
            'screen_type' => $payload['screen_type'] ?? null,
        ];

        if ($type === 'OrderStatus') {
            $context['order_status_id'] = $payload['order_status_id'] ?? null;
        } else {
            $context['order_result_id'] = $payload['order_result_id'] ?? null;
        }

        Log::info("Quest inbound {$type} received", $context);
    }

    private function logSnippet(string $rawBody): string
    {
        if (app()->isProduction()) {
            return '[redacted in production]';
        }

        return substr($rawBody, 0, 400);
    }
}
