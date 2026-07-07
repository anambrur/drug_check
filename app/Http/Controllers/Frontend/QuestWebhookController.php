<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Quest\QuestWebhookService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class QuestWebhookController extends Controller
{
    public function __construct(
        private readonly QuestWebhookService $webhookService
    ) {}

    public function receive(Request $request): Response
    {
        $rawBody = $request->getContent();
        $maxBytes = (int) config('services.quest.webhook.max_body_bytes', 1048576);

        if (strlen($rawBody) > $maxBytes) {
            return response('Payload too large', 413, ['Content-Type' => 'text/plain']);
        }

        try {
            $status = $this->webhookService->handle($rawBody, $request->ip());
        } catch (\Throwable $e) {
            Log::error('Quest webhook unhandled error', ['message' => $e->getMessage()]);
            $status = 'ERROR';
        }

        return response($status, 200, ['Content-Type' => 'text/plain']);
    }
}
