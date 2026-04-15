<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\StripeWebhookEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        if (!$secret) {
            Log::warning('Stripe webhook secret not configured.');
            return response('Webhook secret not configured', 500);
        }

        try {
            $event = Webhook::constructEvent($payload, (string) $sigHeader, $secret);
        } catch (SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        } catch (\UnexpectedValueException $e) {
            return response('Invalid payload', 400);
        }

        // Deduplicate events (Stripe retries)
        $already = StripeWebhookEvent::query()->where('stripe_event_id', $event->id)->exists();
        if ($already) {
            return response('OK', 200);
        }

        StripeWebhookEvent::query()->create([
            'stripe_event_id' => $event->id,
            'type' => $event->type,
            'api_version' => $event->api_version ?? null,
            'livemode' => (bool) ($event->livemode ?? false),
            'stripe_created' => (int) ($event->created ?? 0),
            'payload' => json_decode($payload, true) ?? [],
        ]);

        try {
            $this->processEvent($event);
        } catch (\Throwable $e) {
            Log::error('Stripe webhook processing failed: ' . $e->getMessage(), [
                'stripe_event_id' => $event->id,
                'type' => $event->type,
            ]);
            // Return 200 so Stripe doesn't retry forever; we have the raw event saved.
            return response('OK', 200);
        }

        return response('OK', 200);
    }

    private function processEvent($event): void
    {
        switch ($event->type) {
            case 'payment_intent.succeeded':
            case 'payment_intent.processing':
            case 'payment_intent.payment_failed':
            case 'payment_intent.canceled':
                $this->upsertFromPaymentIntent($event->data->object);
                return;

            case 'charge.refunded':
                $this->markRefundedFromCharge($event->data->object);
                return;

            default:
                return;
        }
    }

    private function upsertFromPaymentIntent($pi): void
    {
        $stripePaymentIntentId = (string) ($pi->id ?? '');
        if ($stripePaymentIntentId === '') {
            return;
        }

        $amount = (int) ($pi->amount ?? 0);
        $currency = (string) ($pi->currency ?? 'usd');
        $status = (string) ($pi->status ?? 'unknown');
        $created = isset($pi->created) ? Carbon::createFromTimestamp((int) $pi->created) : null;

        $chargeId = null;
        if (!empty($pi->latest_charge)) {
            $chargeId = (string) $pi->latest_charge;
        } elseif (!empty($pi->charges->data[0]->id)) {
            $chargeId = (string) $pi->charges->data[0]->id;
        }

        $metadata = (array) ($pi->metadata ?? []);
        $description = !empty($pi->description) ? (string) $pi->description : null;

        $failureMessage = null;
        if (!empty($pi->last_payment_error->message)) {
            $failureMessage = (string) $pi->last_payment_error->message;
        }

        $paidAt = null;
        if ($status === 'succeeded') {
            $paidAt = $created;
        }

        Payment::query()->updateOrCreate(
            ['stripe_payment_intent_id' => $stripePaymentIntentId],
            [
                'portfolio_id' => isset($metadata['portfolio_id']) ? (int) $metadata['portfolio_id'] : null,
                'stripe_charge_id' => $chargeId,
                'amount' => $amount,
                'currency' => $currency,
                'status' => $status,
                'app_tag' => isset($metadata['app_tag']) ? (string) $metadata['app_tag'] : null,
                'app_env' => isset($metadata['app_env']) ? (string) $metadata['app_env'] : null,
                'country' => isset($metadata['country']) && $metadata['country'] !== '' ? (string) $metadata['country'] : null,
                'test_name' => isset($metadata['test_name']) ? (string) $metadata['test_name'] : null,
                'description' => $description,
                'paid_at' => $paidAt,
                'failure_message' => $failureMessage,
                'stripe_payment_intent' => json_decode(json_encode($pi), true),
            ]
        );
    }

    private function markRefundedFromCharge($charge): void
    {
        $paymentIntentId = (string) ($charge->payment_intent ?? '');
        if ($paymentIntentId === '') {
            return;
        }

        $refunded = (bool) ($charge->refunded ?? false);
        $amountRefunded = (int) ($charge->amount_refunded ?? 0);

        if (!$refunded && $amountRefunded <= 0) {
            return;
        }

        Payment::query()
            ->where('stripe_payment_intent_id', $paymentIntentId)
            ->update([
                'refunded_amount' => $amountRefunded,
                'refunded_at' => now(),
                'status' => 'refunded',
            ]);
    }
}

