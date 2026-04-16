<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StripeWebhookEvent extends Model
{
    protected $fillable = [
        'stripe_event_id',
        'payment_intent_id',
        'type',
        'api_version',
        'livemode',
        'stripe_created',
        'payload',
    ];

    protected $casts = [
        'livemode' => 'boolean',
        'payload'  => 'array',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_intent_id', 'stripe_payment_intent_id');
    }
}

