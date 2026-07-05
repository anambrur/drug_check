<?php

namespace App\Models;

use App\Models\Admin\Employee;
use App\Models\Admin\Portfolio;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioTestApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_type',
        'portfolio_id',
        'user_id',
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
    ];

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
}
