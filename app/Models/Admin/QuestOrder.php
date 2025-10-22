<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuestOrder extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'payment_intent_id',
        'quest_order_id',
        'reference_test_id',
        'client_reference_id',
        'order_status',
        'order_result',
        'order_status_updated_at',
        'order_result_updated_at',
        'first_name',
        'last_name',
        'middle_name',
        'primary_id',
        'primary_id_type',
        'dob',
        'primary_phone',
        'secondary_phone',
        'email',
        'zip_code',
        'portfolio_name',
        'unit_codes',
        'dot_test',
        'testing_authority',
        'reason_for_test_id',
        'physical_reason_for_test_id',
        'collection_site_id',
        'observed_requested',
        'split_specimen_requested',
        'order_comments',
        'lab_account',
        'csl',
        'contact_name',
        'telephone_number',
        'end_datetime',
        'end_datetime_timezone_id',
        'expired_at',
        'request_xml',
        'create_response_xml',
        'create_response_status',
        'create_error',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'dob' => 'date',
        'end_datetime' => 'datetime',
        'expired_at' => 'datetime',
        'order_status_updated_at' => 'datetime',
        'order_result_updated_at' => 'datetime',
        'unit_codes' => 'array', // Automatically cast JSON to array
        'create_error' => 'array',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }



    /**
     * Scope a query to only include successful orders.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('create_response_status', 'SUCCESS');
    }

    /**
     * Scope a query to only include failed orders.
     */
    public function scopeFailed($query)
    {
        return $query->where('create_response_status', 'FAILURE');
    }

    /**
     * Check if the order is in a completed state.
     */
    public function getIsCompleteAttribute()
    {
        return !is_null($this->order_result);
    }
}
