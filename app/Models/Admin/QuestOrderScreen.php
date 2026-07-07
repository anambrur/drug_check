<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestOrderScreen extends Model
{
    protected $fillable = [
        'quest_order_id',
        'screen_type',
        'order_status',
        'order_status_datetime',
        'order_result',
        'order_result_datetime',
        'specimen_id',
        'lab_accession_number',
        'collected_datetime',
        'physical_data',
        'status_raw_xml',
        'result_raw_xml',
    ];

    protected $casts = [
        'order_status_datetime' => 'datetime',
        'order_result_datetime' => 'datetime',
        'collected_datetime' => 'datetime',
        'physical_data' => 'array',
    ];

    public function questOrder(): BelongsTo
    {
        return $this->belongsTo(QuestOrder::class);
    }
}
