<?php

namespace App\Services;

use App\Mail\ConsortiumEnrollmentAdminNotification;
use App\Mail\ConsortiumEnrollmentConfirmed;
use App\Models\Admin\ClientProfile;
use App\Models\Admin\ConsortiumPlan;
use App\Models\Admin\HeaderInfo;
use App\Models\ConsortiumEnrollment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ConsortiumEnrollmentService
{
    /**
     * Mark payment complete, create pending company user + inactive client profile,
     * then notify the company and admin.
     */
    public function finalizePaidEnrollment(ConsortiumEnrollment $enrollment, ?string $paymentIntentId = null): void
    {
        DB::transaction(function () use ($enrollment, $paymentIntentId) {
            $enrollment = ConsortiumEnrollment::query()
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

        $this->sendEnrollmentNotifications($enrollment->fresh());
    }

    /**
     * Create a Pending company user and Inactive client profile from enrollment data.
     * Mirrors Admin\ClientProfileController::store patterns.
     */
    protected function createCompanyAccount(ConsortiumEnrollment $enrollment): void
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

            Log::info('Consortium enrollment linked to existing user/profile.', [
                'enrollment_id' => $enrollment->id,
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

        Log::info('Created pending company user and inactive client profile for consortium enrollment.', [
            'enrollment_id' => $enrollment->id,
            'user_id' => $companyUser->id,
            'client_profile_id' => $clientProfile->id,
        ]);
    }

    protected function createClientProfile(ConsortiumEnrollment $enrollment, User $user): ClientProfile
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
            'short_description' => 'Created from Random Consortium enrollment (' . $enrollment->selected_plan . ').',
            'address' => $address,
            'city' => $enrollment->city,
            'state' => $enrollment->state,
            'zip' => $enrollment->zip_code,
            'phone' => $enrollment->phone,
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

    protected function sendEnrollmentNotifications(ConsortiumEnrollment $enrollment): void
    {
        if ($enrollment->notifications_sent_at) {
            return;
        }

        $pricing = ConsortiumPlan::where('name', $enrollment->selected_plan)->with('fees')->first()
            ?? ConsortiumPlan::first();

        if (!$pricing) {
            Log::warning('No consortium plan found for enrollment notification.', [
                'enrollment_id' => $enrollment->id,
                'selected_plan' => $enrollment->selected_plan,
            ]);
            return;
        }

        $companySent = false;
        $adminSent = false;

        try {
            Mail::to($enrollment->email)->send(new ConsortiumEnrollmentConfirmed($enrollment, $pricing));
            $companySent = true;
        } catch (\Exception $e) {
            Log::error('Failed to send company consortium enrollment confirmation email.', [
                'enrollment_id' => $enrollment->id,
                'error' => $e->getMessage(),
            ]);
        }

        try {
            $adminEmail = optional(HeaderInfo::first())->email;
            if ($adminEmail) {
                Mail::to($adminEmail)->send(new ConsortiumEnrollmentAdminNotification($enrollment, $pricing));
                $adminSent = true;
            } else {
                Log::warning('Admin email not configured in HeaderInfo; skipping admin notification.', [
                    'enrollment_id' => $enrollment->id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send admin consortium enrollment notification email.', [
                'enrollment_id' => $enrollment->id,
                'error' => $e->getMessage(),
            ]);
        }

        if ($companySent || $adminSent) {
            $enrollment->update([
                'notifications_sent_at' => now(),
            ]);
        }
    }
}
