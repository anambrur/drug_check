<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuestOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'payment_intent_id',

        // Quest Identifiers
        'quest_order_id',
        'reference_test_id',
        'client_reference_id',

        // Order Status
        'order_status',
        'order_status_screen_type',
        'order_status_datetime',
        'order_status_updated_at',

        // Order Result
        'order_result',
        'order_result_screen_type',
        'order_result_datetime',
        'order_result_updated_at',

        // Specimen
        'specimen_id',
        'lab_accession_number',
        'collected_datetime',

        // Physical JSON
        'physical_data',

        // Raw XML
        'status_raw_xml',
        'result_raw_xml',

        // Donor Info
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

        // Test Info
        'portfolio_id',
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

        // Client Info
        'lab_account',
        'csl',
        'contact_name',
        'telephone_number',

        // Timing
        'end_datetime',
        'end_datetime_timezone_id',
        'expired_at',

        // API Logs
        'request_xml',
        'create_response_xml',
        'create_response_status',
        'create_error',
    ];

    protected $casts = [
        'dob' => 'date',

        // Datetimes
        'order_status_datetime' => 'datetime',
        'order_status_updated_at' => 'datetime',
        'order_result_datetime' => 'datetime',
        'order_result_updated_at' => 'datetime',
        'collected_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'expired_at' => 'datetime',

        // JSON fields
        'unit_codes' => 'array',
        'physical_data' => 'array',
        'create_error' => 'array',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scopes
     */
    public function scopeSuccessful($query)
    {
        return $query->where('create_response_status', 'SUCCESS');
    }

    public function scopeFailed($query)
    {
        return $query->where('create_response_status', 'FAILURE');
    }

    /**
     * Accessors
     */

    // Order completed (based on result)
    public function getIsCompleteAttribute()
    {
        return !is_null($this->order_result);
    }

    // Order has final result
    public function getHasResultAttribute()
    {
        return !empty($this->order_result);
    }

    // Order has status updates
    public function getHasStatusAttribute()
    {
        return !empty($this->order_status);
    }
}
