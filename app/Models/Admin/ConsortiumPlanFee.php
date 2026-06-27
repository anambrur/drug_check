<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsortiumPlanFee extends Model
{
    use HasFactory;

    protected $table = 'consortium_plan_fees';

    protected $fillable = [
        'consortium_plan_id',
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

    /**
     * Relationship: A fee belongs to a plan.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(ConsortiumPlan::class, 'consortium_plan_id');
    }

    /**
     * Accessor: Get fee amount in dollars as float.
     */
    public function getFeeAmountInDollarsAttribute(): float
    {
        return $this->fee_amount / 100;
    }
}
