<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class ConsortiumPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'consortium_plans';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'min_drivers',
        'max_drivers',
        'is_active',
        'display_order',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'min_drivers' => 'integer',
        'max_drivers' => 'integer',
        'display_order' => 'integer',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relationship: One plan has many fees.
     */
    public function fees(): HasMany
    {
        return $this->hasMany(ConsortiumPlanFee::class, 'consortium_plan_id')->orderBy('display_order', 'asc');
    }

    /**
     * Relationship: Scoped flat fees.
     */
    public function flatFees(): HasMany
    {
        return $this->hasMany(ConsortiumPlanFee::class, 'consortium_plan_id')->where('fee_type', 'flat');
    }

    /**
     * Relationship: Scoped per_driver fees.
     */
    public function perDriverFees(): HasMany
    {
        return $this->hasMany(ConsortiumPlanFee::class, 'consortium_plan_id')->where('fee_type', 'per_driver');
    }

    /**
     * Relationship: Created by user.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship: Updated by user.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope: Filter active plans.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Order plans by display_order then name.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('display_order', 'asc')->orderBy('name', 'asc');
    }

    /**
     * Calculate total price in cents for a given driver count.
     */
    public function calculateTotal(int $driverCount): int
    {
        $flatSum = $this->fees->where('fee_type', 'flat')->sum('fee_amount');
        $perDriverSum = $this->fees->where('fee_type', 'per_driver')->sum('fee_amount');

        return (int) ($flatSum + ($perDriverSum * $driverCount));
    }

    /**
     * Get a specific fee's dollar value by fee_key.
     */
    public function getFeeInDollars(string $feeKey): float
    {
        $fee = $this->fees->firstWhere('fee_key', $feeKey);
        return $fee ? $fee->fee_amount_in_dollars : 0.00;
    }
}
