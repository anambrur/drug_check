<?php

namespace App\Services;

use App\Mail\DotTestAdminNotification;
use App\Mail\DotTestPaymentConfirmation;
use App\Mail\NonDotTestAdminNotification;
use App\Mail\NonDotTestPaymentConfirmation;
use App\Models\Admin\ClientProfile;
use App\Models\Admin\CollectionSite;
use App\Models\Admin\Employee;
use App\Models\Admin\HeaderInfo;
use App\Models\Admin\Portfolio;
use App\Models\PortfolioTestApplication;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class PortfolioTestApplicationService
{
    private const REASON_LABELS = [
        1 => 'Pre Employment',
        2 => 'Post Accident',
        3 => 'Random',
        5 => 'Reasonable Cause/Suspicion',
        6 => 'Return to Duty',
        23 => 'Follow Up Test',
        99 => 'Other',
    ];

    public function calculateAmountCents(string|float|null $price): int
    {
        $normalized = preg_replace('/[^0-9.]/', '', (string) $price);
        $amount = (int) round(((float) $normalized) * 100);

        if ($amount < 50) {
            throw new \InvalidArgumentException('Invalid portfolio price.');
        }

        return $amount;
    }

    public function resolveForQuest(int $id): PortfolioTestApplication
    {
        $application = PortfolioTestApplication::with(['portfolio', 'employee.clientProfile'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        if ($application->payment_status !== 'completed') {
            abort(403, 'Payment has not been completed for this application.');
        }

        if (!$application->stripe_payment_intent_id) {
            abort(403, 'Payment reference is missing for this application.');
        }

        $this->verifyStripePaymentIntent($application);

        return $application;
    }

    public function resolveByPaymentIntent(string $paymentIntentId): ?PortfolioTestApplication
    {
        return PortfolioTestApplication::with(['portfolio', 'employee.clientProfile'])
            ->where('user_id', Auth::id())
            ->where('stripe_payment_intent_id', $paymentIntentId)
            ->where('payment_status', 'completed')
            ->first();
    }

    public function verifyStripePaymentIntent(PortfolioTestApplication $application): void
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $paymentIntent = PaymentIntent::retrieve($application->stripe_payment_intent_id);

        if ($paymentIntent->status !== 'succeeded') {
            abort(403, 'Stripe payment has not succeeded.');
        }

        if ((int) ($paymentIntent->amount ?? 0) !== (int) $application->amount) {
            abort(403, 'Payment amount mismatch.');
        }

        if (($paymentIntent->currency ?? null) !== 'usd') {
            abort(403, 'Invalid payment currency.');
        }

        $metadataApplicationId = data_get($paymentIntent, 'metadata.portfolio_test_application_id');
        if ($metadataApplicationId && (string) $metadataApplicationId !== (string) $application->id) {
            abort(403, 'Payment metadata mismatch.');
        }
    }

    /**
     * Populate internal-only fields server-side (Option C).
     */
    public function populateInternalFields(
        PortfolioTestApplication $application,
        Portfolio $portfolio,
        ?Employee $employee = null
    ): array {
        $user = User::find($application->user_id);

        if ($application->isDot() && $employee) {
            $employee->loadMissing('clientProfile');
            // DOT company lives on client_profiles via employees.client_profile_id.
            // Fallback to the ordering user's client profile (users.id -> client_profiles.user_id).
            $profile = $employee->clientProfile
                ?: ($user?->clientProfile ?: ClientProfile::where('user_id', $application->user_id)->first());

            return [
                'gender' => null,
                'address' => $profile?->address,
                'date' => now()->format('m-d-Y'),
                'preferred_location' => trim(implode(', ', array_filter([$profile?->city, $profile?->state, $profile?->zip]))),
                'employee_name' => $profile?->billing_contact_name ?: $profile?->der_contact_name,
                'company_name' => $profile?->company_name,
                'accounting_email' => $profile?->billing_contact_email ?: $profile?->der_contact_email,
                'country' => 'US',
                'reason_for_testing' => $this->reasonLabel($application->reason_for_test_id),
            ];
        }

        return [
            'gender' => null,
            'address' => null,
            'date' => now()->format('m-d-Y'),
            'preferred_location' => null,
            'employee_name' => $user?->name ?? trim(($application->first_name ?? '') . ' ' . ($application->last_name ?? '')),
            'company_name' => $user?->clientProfile?->company_name,
            'accounting_email' => $user?->email ?? $application->email,
            'country' => 'US',
            'reason_for_testing' => $this->reasonLabel($application->reason_for_test_id),
        ];
    }

    /**
     * Build the exact array expected by QuestOrderSubmissionService::submitOrderData().
     */
    public function buildSubmitOrderData(PortfolioTestApplication $application): array
    {
        $application->loadMissing(['portfolio', 'employee.clientProfile']);
        $portfolio = $application->portfolio;

        $isPhysical = str_contains(strtolower($portfolio->title ?? ''), 'physical');
        $endDatetime = $application->end_datetime
            ? $application->end_datetime->format('Y-m-d\TH:i')
            : null;

        $dotTest = $application->isDot() ? 'T' : 'F';
        $storedDotTest = $application->dot_test ?? $dotTest;
        if ($storedDotTest !== $dotTest) {
            throw new \RuntimeException(
                'DOTTest flag does not match the application test type. Refusing to submit a mismatched Quest order.'
            );
        }

        $observed = $application->observed_requested ?? 'N';
        if ($dotTest === 'T' && in_array((int) $application->reason_for_test_id, [6, 23], true)) {
            $observed = 'Y';
        }

        return [
            'portfolio_id' => $application->portfolio_id,
            'payment_intent_id' => $application->stripe_payment_intent_id,
            'first_name' => $application->first_name,
            'last_name' => $application->last_name,
            'middle_name' => $application->middle_name,
            'email' => $application->email,
            'primary_phone' => $application->phone,
            'secondary_phone' => $application->secondary_phone,
            'primary_id' => $application->primary_id,
            'primary_id_type' => $isPhysical ? $application->primary_id_type : null,
            'dob' => $application->dob,
            'zip_code' => $application->zip_code,
            'dot_test' => $dotTest,
            'testing_authority' => $application->testing_authority,
            'reason_for_test_id' => $isPhysical ? null : $application->reason_for_test_id,
            'physical_reason_for_test_id' => $isPhysical ? $application->physical_reason_for_test_id : null,
            'collection_site_id' => $isPhysical ? null : $application->collection_site_id,
            'end_datetime' => $endDatetime,
            'end_datetime_timezone_id' => $application->end_datetime_timezone_id,
            'observed_requested' => $observed,
            'split_specimen_requested' => $application->split_specimen_requested ?? 'N',
            'unit_codes' => $portfolio->code,
            'csl' => $application->csl ?? config('services.quest.default_csl'),
            'contact_name' => $application->contact_name ?? config('services.quest.default_contact_name'),
            'telephone_number' => $application->telephone_number ?? config('services.quest.default_telephone'),
            'order_comments' => $application->order_comments,
            'response_url' => url('/api/quest/order-status'),
            'lab_account' => $this->resolveLabAccount($application),
        ];
    }

    public function resolveLabAccount(PortfolioTestApplication $application): string
    {
        if (!app()->isProduction()) {
            $account = $application->isDot()
                ? config('services.quest.dot_lab_account')
                : config('services.quest.lab_account');

            return $this->requireLabAccount($account, $application->isDot() ? 'QUEST_DOT_LAB_ACCOUNT' : 'QUEST_LAB_ACCOUNT');
        }

        if ($application->isDot()) {
            $application->loadMissing('employee.clientProfile');

            $account = $application->employee?->clientProfile?->account_no
                ?? config('services.quest.dot_lab_account');

            return $this->requireLabAccount($account, 'client profile account_no / QUEST_DOT_LAB_ACCOUNT');
        }

        $account = $application->portfolio?->lab_account ?? config('services.quest.lab_account');

        return $this->requireLabAccount($account, 'portfolio lab_account / QUEST_LAB_ACCOUNT');
    }

    private function requireLabAccount(mixed $account, string $source): string
    {
        $account = trim((string) ($account ?? ''));

        if ($account === '') {
            throw new \RuntimeException("Quest LabAccount is not configured ({$source}).");
        }

        return $account;
    }

    public function portfolioFlags(Portfolio $portfolio): array
    {
        $title = strtolower($portfolio->title ?? '');

        return [
            'is_physical' => str_contains($title, 'physical'),
            'is_ebat' => str_contains($title, 'ebat'),
        ];
    }

    /**
     * Form default values for quest order fields, pre-filled from a saved application.
     *
     * @return array<string, mixed>
     */
    public function questDefaultsFromApplication(PortfolioTestApplication $application): array
    {
        $endDatetime = $application->end_datetime
            ? $application->end_datetime->format('Y-m-d\TH:i')
            : null;

        return [
            'employee_id' => $application->employee_id,
            'first_name' => $application->first_name,
            'last_name' => $application->last_name,
            'middle_name' => $application->middle_name,
            'primary_id' => $application->primary_id,
            'primary_id_type' => $application->primary_id_type,
            'dob' => $application->dob,
            'email' => $application->email,
            'primary_phone' => $application->phone,
            'secondary_phone' => $application->secondary_phone,
            'zip_code' => $application->zip_code,
            'dot_test' => $application->dot_test ?? ($application->isDot() ? 'T' : 'F'),
            'testing_authority' => $application->testing_authority,
            'reason_for_test_id' => $application->reason_for_test_id,
            'physical_reason_for_test_id' => $application->physical_reason_for_test_id,
            'collection_site_id' => $application->collection_site_id,
            'end_datetime' => $endDatetime,
            'end_datetime_timezone_id' => $application->end_datetime_timezone_id,
            'observed_requested' => $application->observed_requested ?? 'N',
            'split_specimen_requested' => $application->split_specimen_requested ?? 'N',
            'csl' => $application->csl ?? config('services.quest.default_csl'),
            'contact_name' => $application->contact_name ?? config('services.quest.default_contact_name'),
            'telephone_number' => $application->telephone_number ?? config('services.quest.default_telephone'),
            'order_comments' => $application->order_comments,
        ];
    }

    /**
     * Select2 initial option for the application's collection site, if any.
     *
     * @return array{id: string, text: string}|null
     */
    public function collectionSiteSelectOption(PortfolioTestApplication $application): ?array
    {
        if (!$application->collection_site_id) {
            return null;
        }

        $site = CollectionSite::where('collection_site_code', $application->collection_site_id)->first();

        if ($site) {
            return [
                'id' => $site->collection_site_code,
                'text' => $this->formatCollectionSiteLabel($site),
            ];
        }

        return [
            'id' => $application->collection_site_id,
            'text' => $application->collection_site_id,
        ];
    }

    /**
     * Active employees the user may select for DOT checkout / retry.
     */
    public function employeesForUser(?User $user = null): Collection
    {
        $user = $user ?? Auth::user();
        if (!$user) {
            return collect();
        }

        $role = $user->roles()->first();

        return match ($role?->name) {
            'super-admin' => Employee::with('clientProfile')->where('status', 'active')->get(),
            'company' => Employee::with('clientProfile')
                ->where('status', 'active')
                ->where('client_profile_id', ClientProfile::where('user_id', $user->id)->value('id'))
                ->get(),
            default => collect(),
        };
    }

    /**
     * Map validated Quest form input to portfolio_test_applications columns.
     *
     * @return array<string, mixed>
     */
    public function questAttributesFromValidated(array $validated): array
    {
        $testType = $validated['test_type'] ?? 'non_dot';

        return [
            'employee_id' => $testType === 'dot' ? (int) $validated['employee_id'] : null,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'middle_name' => $this->nullIfEmpty($validated['middle_name'] ?? null),
            'primary_id' => $validated['primary_id'],
            'primary_id_type' => $this->nullIfEmpty($validated['primary_id_type'] ?? null),
            'dob' => $this->nullIfEmpty($validated['dob'] ?? null),
            'email' => $validated['email'],
            'phone' => $this->nullIfEmpty($validated['primary_phone'] ?? null),
            'secondary_phone' => $this->nullIfEmpty($validated['secondary_phone'] ?? null),
            'zip_code' => $this->nullIfEmpty($validated['zip_code'] ?? null),
            'dot_test' => $validated['dot_test'],
            'testing_authority' => $this->nullIfEmpty($validated['testing_authority'] ?? null),
            'reason_for_test_id' => $this->nullInt($validated['reason_for_test_id'] ?? null),
            'physical_reason_for_test_id' => $this->nullIfEmpty($validated['physical_reason_for_test_id'] ?? null),
            'collection_site_id' => $this->nullIfEmpty($validated['collection_site_id'] ?? null),
            'end_datetime' => $this->nullIfEmpty($validated['end_datetime'] ?? null),
            'end_datetime_timezone_id' => $this->nullInt($validated['end_datetime_timezone_id'] ?? null),
            'observed_requested' => $this->nullIfEmpty($validated['observed_requested'] ?? null) ?? 'N',
            'split_specimen_requested' => $this->nullIfEmpty($validated['split_specimen_requested'] ?? null) ?? 'N',
            'csl' => $this->nullIfEmpty($validated['csl'] ?? null),
            'contact_name' => $this->nullIfEmpty($validated['contact_name'] ?? null),
            'telephone_number' => $this->nullIfEmpty($validated['telephone_number'] ?? null),
            'order_comments' => $this->nullIfEmpty($validated['order_comments'] ?? null),
        ];
    }

    /**
     * Mark payment complete (idempotent), create in-app admin toast, then send
     * customer + admin emails. Safe to call from both success page and webhook.
     */
    public function finalizePaidApplication(PortfolioTestApplication $application, ?string $paymentIntentId = null): void
    {
        $this->markPaymentCompleted($application, $paymentIntentId);
        $application->refresh();
        $this->sendApplicationNotifications($application);
    }

    public function markPaymentCompleted(PortfolioTestApplication $application, ?string $paymentIntentId = null): void
    {
        if ($application->payment_status === 'completed') {
            if ($paymentIntentId && !$application->stripe_payment_intent_id) {
                $application->update([
                    'stripe_payment_intent_id' => $paymentIntentId,
                ]);
            }

            return;
        }

        $application->update([
            'payment_status' => 'completed',
            'status' => 'Payment Completed',
            'stripe_payment_intent_id' => $paymentIntentId ?: $application->stripe_payment_intent_id,
        ]);

        $application->refresh();

        app(AdminOrderNotificationService::class)->notifyPaidApplication($application);
    }

    protected function sendApplicationNotifications(PortfolioTestApplication $application): void
    {
        $application->loadMissing('portfolio');

        $customerEmail = trim((string) $application->email);

        if (!$application->customer_notified_at) {
            if ($customerEmail !== '' && filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
                try {
                    $customerMail = $application->isDot()
                        ? new DotTestPaymentConfirmation($application)
                        : new NonDotTestPaymentConfirmation($application);

                    Mail::to($customerEmail)->send($customerMail);
                    $application->update(['customer_notified_at' => now()]);
                } catch (\Throwable $e) {
                    Log::error('Failed to send portfolio test customer payment confirmation email.', [
                        'application_id' => $application->id,
                        'test_type' => $application->test_type,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        if (!$application->admin_notified_at) {
            try {
                $adminEmail = trim((string) (optional(HeaderInfo::first())->email ?? ''));

                if ($adminEmail !== '' && filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
                    $adminMail = $application->isDot()
                        ? new DotTestAdminNotification($application)
                        : new NonDotTestAdminNotification($application);

                    Mail::to($adminEmail)->send($adminMail);
                    $application->update(['admin_notified_at' => now()]);
                }
            } catch (\Throwable $e) {
                Log::error('Failed to send portfolio test admin notification email.', [
                    'application_id' => $application->id,
                    'test_type' => $application->test_type,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $application->refresh();

        if ($application->customer_notified_at && $application->admin_notified_at && !$application->notifications_sent_at) {
            $application->update(['notifications_sent_at' => now()]);
        }
    }

    private function reasonLabel(?int $reasonId): string
    {
        if (!$reasonId) {
            return 'Not specified';
        }

        return self::REASON_LABELS[$reasonId] ?? 'Other';
    }

    private function formatCollectionSiteLabel(CollectionSite $site): string
    {
        $parts = array_filter([
            $site->name,
            implode(', ', array_filter([$site->address_1, $site->city, $site->state, $site->zip_code])),
        ]);

        return implode(' — ', $parts);
    }

    private function nullIfEmpty(mixed $value): mixed
    {
        if ($value === '' || $value === null) {
            return null;
        }

        return $value;
    }

    private function nullInt(mixed $value): ?int
    {
        if ($value === '' || $value === null) {
            return null;
        }

        return (int) $value;
    }
}
