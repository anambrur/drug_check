<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'portfolio_id',
        'stripe_payment_intent_id',
        'stripe_charge_id',
        'amount',
        'currency',
        'status',
        'app_tag',
        'app_env',
        'customer_name',
        'customer_email',
        'customer_phone',
        'country',
        'test_name',
        'description',
        'paid_at',
        'refunded_amount',
        'refunded_at',
        'failure_message',
        'stripe_payment_intent',
    ];

    protected $casts = [
        'paid_at'               => 'datetime',
        'refunded_at'           => 'datetime',
        'stripe_payment_intent' => 'array',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    /**
     * All Stripe webhook events that belong to this PaymentIntent.
     */
    public function webhookEvents()
    {
        return $this->hasMany(StripeWebhookEvent::class, 'payment_intent_id', 'stripe_payment_intent_id')
                    ->orderBy('stripe_created');
    }

    // ── Accessors ────────────────────────────────────────────────────────────

    /**
     * Amount formatted as a USD string, e.g. "$49.00".
     */
    protected function formattedAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => '$' . number_format($this->amount / 100, 2)
        );
    }

    /**
     * Bootstrap badge class for the payment status.
     */
    protected function statusBadgeClass(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->status) {
                'succeeded'  => 'badge-success',
                'processing' => 'badge-info',
                'refunded'   => 'badge-warning',
                'canceled'   => 'badge-secondary',
                default      => 'badge-danger',
            }
        );
    }

    /**
     * Best display name for the customer.
     */
    protected function customerDisplayName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->customer_name ?: ($this->customer_email ?: '—')
        );
    }
}

