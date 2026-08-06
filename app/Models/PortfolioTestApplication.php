<?php

namespace App\Models;

use App\Models\Admin\ClientProfile;
use App\Models\Admin\Employee;
use App\Models\Admin\Portfolio;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PortfolioTestApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_type',
        'portfolio_id',
        'user_id',
        'is_guest',
        'guest_access_token',
        'employee_id',
        'first_name',
        'last_name',
        'middle_name',
        'primary_id',
        'primary_id_type',
        'dob',
        'email',
        'phone',
        'secondary_phone',
        'address',
        'date',
        'gender',
        'preferred_location',
        'employee_name',
        'company_name',
        'accounting_email',
        'reason_for_testing',
        'country',
        'dot_test',
        'testing_authority',
        'reason_for_test_id',
        'physical_reason_for_test_id',
        'collection_site_id',
        'end_datetime',
        'end_datetime_timezone_id',
        'observed_requested',
        'split_specimen_requested',
        'csl',
        'contact_name',
        'telephone_number',
        'order_comments',
        'amount',
        'stripe_checkout_session_id',
        'stripe_payment_intent_id',
        'payment_status',
        'status',
        'quest_submission_status',
        'quest_submission_error',
        'quest_order_id',
    ];

    protected $casts = [
        'end_datetime' => 'datetime',
        'reason_for_test_id' => 'integer',
        'end_datetime_timezone_id' => 'integer',
        'is_guest' => 'boolean',
    ];

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Company profile linked to the ordering account.
     * portfolio_test_applications.user_id -> client_profiles.user_id
     */
    public function clientProfile(): HasOne
    {
        return $this->hasOne(ClientProfile::class, 'user_id', 'user_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    protected function formattedAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => '$' . number_format($this->amount / 100, 2)
        );
    }

    public function applicantDisplayName(): string
    {
        $name = trim(implode(' ', array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ])));

        if ($name !== '') {
            return $name;
        }

        return $this->employee_name
            ?: trim(implode(' ', array_filter([
                $this->employee?->first_name,
                $this->employee?->middle_name,
                $this->employee?->last_name,
            ])))
            ?: '—';
    }

    /**
     * Resolve company name using ClientProfile schema:
     * 1. Denormalized company_name on the application
     * 2. DOT employee path: employees.client_profile_id -> client_profiles.company_name
     * 3. Ordering user path: users.id = client_profiles.user_id -> company_name
     */
    public function resolveCompanyName(): string
    {
        if (filled($this->company_name)) {
            return (string) $this->company_name;
        }

        $this->loadMissing(['employee.clientProfile', 'clientProfile', 'user.clientProfile']);

        $fromEmployee = $this->employee?->clientProfile?->company_name;
        if (filled($fromEmployee)) {
            return (string) $fromEmployee;
        }

        $fromUser = $this->clientProfile?->company_name
            ?: $this->user?->clientProfile?->company_name;

        if (filled($fromUser)) {
            return (string) $fromUser;
        }

        return '—';
    }

    public function isDot(): bool
    {
        return $this->test_type === 'dot';
    }

    public function isNonDot(): bool
    {
        return $this->test_type === 'non_dot';
    }

    public function isQuestSubmitted(): bool
    {
        return $this->quest_submission_status === 'submitted';
    }

    public function guestSessionMatches(): bool
    {
        if (!$this->is_guest || !$this->guest_access_token) {
            return false;
        }

        $tokens = session('portfolio_guest_tokens', []);

        return isset($tokens[$this->id])
            && hash_equals($this->guest_access_token, $tokens[$this->id]);
    }

    public static function storeGuestSessionToken(self $application): void
    {
        $tokens = session('portfolio_guest_tokens', []);
        $tokens[$application->id] = $application->guest_access_token;
        session(['portfolio_guest_tokens' => $tokens]);
    }
}
