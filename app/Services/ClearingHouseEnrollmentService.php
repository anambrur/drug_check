<?php

namespace App\Services;

use App\Mail\ClearingHouseEnrollmentAdminNotification;
use App\Mail\ClearingHouseEnrollmentConfirmed;
use App\Models\Admin\ClearingHousePlan;
use App\Models\Admin\ClientProfile;
use App\Models\Admin\HeaderInfo;
use App\Models\ClearingHouseEnrollment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ClearingHouseEnrollmentService
{
    /**
     * Mark payment complete, create pending company user + inactive client profile,
     * then notify the company and admin.
     */
    public function finalizePaidEnrollment(ClearingHouseEnrollment $enrollment, ?string $paymentIntentId = null): void
    {
        DB::transaction(function () use ($enrollment, $paymentIntentId) {
            $enrollment = ClearingHouseEnrollment::query()
                ->whereKey($enrollment->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($enrollment->payment_status !== 'completed') {
                $enrollment->update([
                    'payment_status' => 'completed',
                    'status' => 'Payment Completed',
                    'stripe_payment_intent_id' => $paymentIntentId ?? $enrollment->stripe_payment_intent_id,
                ]);
            } elseif ($paymentIntentId && !$enrollment->stripe_payment_intent_id) {
                $enrollment->update([
                    'stripe_payment_intent_id' => $paymentIntentId,
                ]);
            }

            $enrollment->refresh();

            if (!$enrollment->user_id || !$enrollment->client_profile_id) {
                $this->createCompanyAccount($enrollment);
                $enrollment->refresh();
            }
        });

        $enrollment = $enrollment->fresh();

        app(AdminOrderNotificationService::class)->notifyPaidClearingHouse($enrollment);

        $this->sendEnrollmentNotifications($enrollment);
    }

    protected function createCompanyAccount(ClearingHouseEnrollment $enrollment): void
    {
        $existingUser = User::where('email', $enrollment->email)->first();

        if ($existingUser) {
            $clientProfile = $existingUser->clientProfile;

            if (!$clientProfile) {
                $clientProfile = $this->createClientProfile($enrollment, $existingUser);
            }

            $enrollment->update([
                'user_id' => $existingUser->id,
                'client_profile_id' => $clientProfile->id,
            ]);

            return;
        }

        $randomPassword = chr(rand(65, 90)) . rand(1000, 9999) . chr(rand(65, 90)) . rand(100, 999);

        $companyUser = User::factory()->create([
            'name' => $enrollment->company_name,
            'email' => $enrollment->email,
            'password' => Hash::make($randomPassword),
            'type' => 2,
            'status' => 2, // Pending
        ]);

        $companyUser->assignRole('company');

        $clientProfile = $this->createClientProfile($enrollment, $companyUser);

        $enrollment->update([
            'user_id' => $companyUser->id,
            'client_profile_id' => $clientProfile->id,
        ]);
    }

    protected function createClientProfile(ClearingHouseEnrollment $enrollment, User $user): ClientProfile
    {
        $address = trim(implode(', ', array_filter([
            $enrollment->address_line_1,
            $enrollment->address_line_2,
        ])));

        $derName = trim($enrollment->first_name . ' ' . $enrollment->last_name);

        return ClientProfile::create([
            'user_id' => $user->id,
            'company_name' => $enrollment->company_name,
            'account_no' => $enrollment->dot_number,
            'short_description' => 'Created from Clearing House enrollment (' . $enrollment->selected_plan . ').',
            'address' => $address,
            'city' => $enrollment->city,
            'state' => $enrollment->state,
            'zip' => $enrollment->zip_code,
            'phone' => $enrollment->company_phone ?: $enrollment->phone,
            'fax' => null,
            'dot_agency_id' => null,
            'shipping_address' => $address,
            'billing_contact_name' => $derName,
            'billing_contact_email' => $enrollment->email,
            'billing_contact_phone' => $enrollment->phone,
            'der_contact_name' => $derName,
            'der_contact_email' => $enrollment->email,
            'der_contact_phone' => $enrollment->phone,
            'client_start_date' => now()->toDateString(),
            'certificate_start_date' => null,
            'status' => 'inactive',
        ]);
    }

    protected function sendEnrollmentNotifications(ClearingHouseEnrollment $enrollment): void
    {
        if (!$enrollment) {
            return;
        }

        $pricing = ClearingHousePlan::where('name', $enrollment->selected_plan)->with('fees')->first()
            ?? ClearingHousePlan::first();

        if (!$pricing) {
            return;
        }

        $companyEmail = trim((string) $enrollment->email);

        if (!$enrollment->company_notified_at) {
            if ($companyEmail !== '' && filter_var($companyEmail, FILTER_VALIDATE_EMAIL)) {
                try {
                    Mail::to($companyEmail)->send(new ClearingHouseEnrollmentConfirmed($enrollment, $pricing));
                    $enrollment->update(['company_notified_at' => now()]);
                } catch (\Throwable $e) {
                    Log::error('Failed to send company clearing house enrollment confirmation email.', [
                        'enrollment_id' => $enrollment->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        if (!$enrollment->admin_notified_at) {
            try {
                $adminEmail = trim((string) (optional(HeaderInfo::first())->email ?? ''));

                if ($adminEmail !== '' && filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($adminEmail)->send(new ClearingHouseEnrollmentAdminNotification($enrollment, $pricing));
                    $enrollment->update(['admin_notified_at' => now()]);
                }
            } catch (\Throwable $e) {
                Log::error('Failed to send admin clearing house enrollment notification email.', [
                    'enrollment_id' => $enrollment->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $enrollment->refresh();

        if ($enrollment->company_notified_at && $enrollment->admin_notified_at && !$enrollment->notifications_sent_at) {
            $enrollment->update(['notifications_sent_at' => now()]);
        }
    }
}
