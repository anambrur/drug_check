<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
        'stripe_payment_intent' => 'array',
    ];
}

