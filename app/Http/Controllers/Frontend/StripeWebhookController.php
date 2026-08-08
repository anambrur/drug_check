<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\StripeWebhookEvent;
use App\Models\PortfolioTestApplication;
use App\Models\ConsortiumEnrollment;
use App\Models\ClearingHouseEnrollment;
use App\Services\ConsortiumEnrollmentService;
use App\Services\ClearingHouseEnrollmentService;
use App\Services\PortfolioTestApplicationService;
use App\Services\QuestOrderSubmissionService;
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

        // Extract the PaymentIntent ID if this event relates to one
        $eventObject    = $event->data->object ?? null;
        $paymentIntentId = null;
        if (!empty($eventObject->id) && str_starts_with((string) $eventObject->id, 'pi_')) {
            $paymentIntentId = (string) $eventObject->id;
        } elseif (!empty($eventObject->payment_intent)) {
            $paymentIntentId = (string) $eventObject->payment_intent;
        }

        StripeWebhookEvent::query()->create([
            'stripe_event_id'    => $event->id,
            'payment_intent_id'  => $paymentIntentId,
            'type'               => $event->type,
            'api_version'        => $event->api_version ?? null,
            'livemode'           => (bool) ($event->livemode ?? false),
            'stripe_created'     => (int) ($event->created ?? 0),
            'payload'            => json_decode($payload, true) ?? [],
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
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event->data->object);
                return;

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

        $amount   = (int) ($pi->amount ?? 0);
        $currency = (string) ($pi->currency ?? 'usd');
        $status   = (string) ($pi->status ?? 'unknown');
        $created  = isset($pi->created) ? Carbon::createFromTimestamp((int) $pi->created) : null;

        // ── Charge & billing details ────────────────────────────────────────
        // Stripe puts the richest customer data inside billing_details on
        // the charge object.  We check both the expanded charges array and
        // the newer latest_charge scalar.
        $chargeId      = null;
        $billingDetails = null;

        if (!empty($pi->charges->data[0])) {
            $charge         = $pi->charges->data[0];
            $chargeId       = (string) ($charge->id ?? '');
            $billingDetails = $charge->billing_details ?? null;
        }

        // latest_charge overrides if both are present (newer API shape)
        if (!empty($pi->latest_charge)) {
            $chargeId = (string) $pi->latest_charge;
        }

        // ── Metadata ────────────────────────────────────────────────────────
        // StripeObject must use toArray(); (array) cast exposes internal props only.
        $metadata = [];
        if (is_object($pi->metadata ?? null) && method_exists($pi->metadata, 'toArray')) {
            $metadata = $pi->metadata->toArray();
        } elseif (is_array($pi->metadata ?? null)) {
            $metadata = $pi->metadata;
        }

        $application = null;
        $applicationId = isset($metadata['portfolio_test_application_id'])
            ? (int) $metadata['portfolio_test_application_id']
            : 0;
        if ($applicationId > 0) {
            $application = PortfolioTestApplication::with('portfolio')->find($applicationId);
        }

        // ── Customer name / email / phone ───────────────────────────────────
        // Priority: billing_details > PI metadata > receipt_email > local application
        $customerName = !empty($billingDetails->name)
            ? (string) $billingDetails->name
            : (!empty($metadata['customer_name']) ? (string) $metadata['customer_name'] : null);

        $customerEmail = !empty($billingDetails->email)
            ? (string) $billingDetails->email
            : (!empty($pi->receipt_email)
                ? (string) $pi->receipt_email
                : (!empty($metadata['customer_email']) ? (string) $metadata['customer_email'] : null));

        $customerPhone = !empty($billingDetails->phone)
            ? (string) $billingDetails->phone
            : (!empty($metadata['customer_phone']) ? (string) $metadata['customer_phone'] : null);

        if ($application) {
            if ($customerName === null) {
                $customerName = trim(implode(' ', array_filter([
                    $application->first_name,
                    $application->last_name,
                ]))) ?: null;
            }
            if ($customerEmail === null && !empty($application->email)) {
                $customerEmail = (string) $application->email;
            }
            if ($customerPhone === null && !empty($application->phone)) {
                $customerPhone = (string) $application->phone;
            }
        }

        $userId = null;
        if (!empty($metadata['user_id'])) {
            $userId = (int) $metadata['user_id'];
        } elseif ($application?->user_id) {
            $userId = (int) $application->user_id;
        }

        $portfolioId = null;
        if (!empty($metadata['portfolio_id'])) {
            $portfolioId = (int) $metadata['portfolio_id'];
        } elseif ($application?->portfolio_id) {
            $portfolioId = (int) $application->portfolio_id;
        }

        $testName = !empty($metadata['test_name'])
            ? (string) $metadata['test_name']
            : ($application?->portfolio?->title ? (string) $application->portfolio->title : null);

        $country = !empty($metadata['country'])
            ? (string) $metadata['country']
            : ($application?->country ? (string) $application->country : null);

        // ── Other fields ────────────────────────────────────────────────────
        $description = !empty($pi->description) ? (string) $pi->description : null;

        $failureMessage = null;
        if (!empty($pi->last_payment_error->message)) {
            $failureMessage = (string) $pi->last_payment_error->message;
        }

        $paidAt = ($status === 'succeeded') ? $created : null;

        Payment::query()->updateOrCreate(
            ['stripe_payment_intent_id' => $stripePaymentIntentId],
            [
                'user_id'        => $userId,
                'portfolio_id'   => $portfolioId,
                'stripe_charge_id' => $chargeId ?: null,
                'amount'         => $amount,
                'currency'       => $currency,
                'status'         => $status,
                'app_tag'        => isset($metadata['app_tag'])  ? (string) $metadata['app_tag']  : null,
                'app_env'        => isset($metadata['app_env'])  ? (string) $metadata['app_env']  : null,
                'country'        => $country,
                'test_name'      => $testName,
                'customer_name'  => $customerName,
                'customer_email' => $customerEmail,
                'customer_phone' => $customerPhone,
                'description'    => $description,
                'paid_at'        => $paidAt,
                'failure_message'=> $failureMessage,
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

    /**
     * Handle completed checkout session event.
     */
    private function handleCheckoutSessionCompleted($session): void
    {
        $portfolioApplicationId = $session->metadata->portfolio_test_application_id ?? null;
        if ($portfolioApplicationId) {
            $this->handlePortfolioTestCheckoutCompleted($session, (int) $portfolioApplicationId);
        }

        $consortiumEnrollmentId = $session->metadata->consortium_enrollment_id ?? null;
        if ($consortiumEnrollmentId) {
            $this->finalizeConsortiumEnrollment($session, (int) $consortiumEnrollmentId);
        }

        $clearingHouseEnrollmentId = $session->metadata->clearing_house_enrollment_id ?? null;
        if ($clearingHouseEnrollmentId) {
            $this->finalizeClearingHouseEnrollment($session, (int) $clearingHouseEnrollmentId);
        }
    }

    private function finalizeConsortiumEnrollment($session, int $enrollmentId): void
    {
        $enrollment = ConsortiumEnrollment::find($enrollmentId);
        if (!$enrollment) {
            Log::error('Consortium enrollment not found during webhook processing.', ['id' => $enrollmentId]);
            return;
        }

        if ($enrollment->payment_status === 'completed'
            && $enrollment->user_id
            && $enrollment->client_profile_id
            && $enrollment->company_notified_at
            && $enrollment->admin_notified_at) {
            return;
        }

        try {
            app(ConsortiumEnrollmentService::class)->finalizePaidEnrollment(
                $enrollment,
                $session->payment_intent ?? null
            );
        } catch (\Throwable $e) {
            Log::error('Failed to finalize consortium enrollment after checkout.', [
                'enrollment_id' => $enrollmentId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function finalizeClearingHouseEnrollment($session, int $enrollmentId): void
    {
        $enrollment = ClearingHouseEnrollment::find($enrollmentId);
        if (!$enrollment) {
            Log::error('Clearing house enrollment not found during webhook processing.', ['id' => $enrollmentId]);
            return;
        }

        if ($enrollment->payment_status === 'completed'
            && $enrollment->user_id
            && $enrollment->client_profile_id
            && $enrollment->company_notified_at
            && $enrollment->admin_notified_at) {
            return;
        }

        try {
            app(ClearingHouseEnrollmentService::class)->finalizePaidEnrollment(
                $enrollment,
                $session->payment_intent ?? null
            );
        } catch (\Throwable $e) {
            Log::error('Failed to finalize clearing house enrollment after checkout.', [
                'enrollment_id' => $enrollmentId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function handlePortfolioTestCheckoutCompleted($session, int $applicationId): void
    {
        $application = PortfolioTestApplication::with('portfolio')->find($applicationId);
        if (!$application) {
            Log::error('Portfolio test application not found during webhook processing.', ['id' => $applicationId]);
            return;
        }

        app(PortfolioTestApplicationService::class)->finalizePaidApplication(
            $application,
            $session->payment_intent ?? null
        );
        $application->refresh();

        if ($application->quest_submission_status === 'submitted') {
            return;
        }

        try {
            app(QuestOrderSubmissionService::class)->submitFromApplication($application);
        } catch (\Throwable $e) {
            Log::error('Quest auto-submit via webhook failed', [
                'application_id' => $applicationId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

