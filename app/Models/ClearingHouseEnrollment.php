<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClearingHouseEnrollment extends Model
{
    use HasFactory;

    protected $table = 'clearing_house_enrollments';

    protected $fillable = [
        'user_id',
        'client_profile_id',
        'company_name',
        'dba_name',
        'dot_number',
        'mc_number',
        'ein_number',
        'company_phone',
        'first_name',
        'last_name',
        'job_title',
        'email',
        'phone',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'zip_code',
        'is_owner_operator',
        'clearinghouse_registered',
        'authorize_conduct_queries',
        'authorize_report_violations',
        'authorize_report_rtd',
        'acknowledge_designate_ctpa',
        'acknowledge_query_plan',
        'selected_plan',
        'driver_count',
        'notes',
        'amount',
        'stripe_checkout_session_id',
        'stripe_payment_intent_id',
        'status',
        'payment_status',
        'internal_notes',
        'notifications_sent_at',
        'company_notified_at',
        'admin_notified_at',
    ];

    protected $casts = [
        'is_owner_operator' => 'boolean',
        'authorize_conduct_queries' => 'boolean',
        'authorize_report_violations' => 'boolean',
        'authorize_report_rtd' => 'boolean',
        'acknowledge_designate_ctpa' => 'boolean',
        'acknowledge_query_plan' => 'boolean',
        'notifications_sent_at' => 'datetime',
        'company_notified_at' => 'datetime',
        'admin_notified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function clientProfile()
    {
        return $this->belongsTo(\App\Models\Admin\ClientProfile::class);
    }

    protected function formattedAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => '$' . number_format($this->amount / 100, 2)
        );
    }

    protected function statusBadgeClass(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->status) {
                'Active'             => 'badge badge-pill badge-success',
                'Payment Completed'  => 'badge badge-pill badge-info',
                'Under Review'       => 'badge badge-pill badge-warning',
                'Credentials Sent'   => 'badge badge-pill badge-primary',
                'Contacted'          => 'badge badge-pill badge-secondary',
                'Pending Payment'    => 'badge badge-pill badge-danger',
                'Cancelled'          => 'badge badge-pill badge-dark',
                default              => 'badge badge-pill badge-secondary',
            }
        );
    }
}
