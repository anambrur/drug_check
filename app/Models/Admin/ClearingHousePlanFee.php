<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClearingHousePlanFee extends Model
{
    use HasFactory;

    protected $table = 'clearing_house_plan_fees';

    protected $fillable = [
        'clearing_house_plan_id',
        'fee_key',
        'fee_label',
        'fee_amount',
        'fee_type',
        'display_order',
    ];

    protected $casts = [
        'fee_amount' => 'integer',
        'display_order' => 'integer',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(ClearingHousePlan::class, 'clearing_house_plan_id');
    }

    public function getFeeAmountInDollarsAttribute(): float
    {
        return $this->fee_amount / 100;
    }
}
