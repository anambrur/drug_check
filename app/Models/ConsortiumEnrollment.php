<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ConsortiumEnrollment extends Model
{
    use HasFactory;

    protected $table = 'consortium_enrollments';

    protected $fillable = [
        'user_id',
        'client_profile_id',
        'company_name',
        'dba_name',
        'dot_number',
        'mc_number',
        'ein_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'zip_code',
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

    /**
     * Amount formatted as USD, e.g., "$122.50".
     */
    protected function formattedAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => '$' . number_format($this->amount / 100, 2)
        );
    }

    /**
     * Bootstrap badge class for the enrollment/payment status.
     */
    protected function statusBadgeClass(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->status) {
                'Active'             => 'badge badge-success text-success bg-success-light',
                'Payment Completed'  => 'badge badge-info text-info bg-info-light',
                'Under Review'       => 'badge badge-warning text-warning bg-warning-light',
                'Credentials Sent'   => 'badge badge-primary text-primary bg-primary-light',
                'Contacted'          => 'badge badge-secondary text-secondary bg-secondary-light',
                'Pending Payment'    => 'badge badge-danger text-danger bg-danger-light',
                'Cancelled'          => 'badge badge-dark text-dark bg-dark-light',
                default              => 'badge badge-light text-muted bg-light',
            }
        );
    }
}
