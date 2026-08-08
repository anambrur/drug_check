<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class QuestWebhookDeadLetter extends Model
{
    protected $fillable = [
        'payload_type',
        'quest_order_id',
        'reference_test_id',
        'client_reference_id',
        'screen_type',
        'status_or_result_id',
        'raw_body',
        'client_ip',
        'reason',
        'replayed_at',
    ];

    protected $casts = [
        'replayed_at' => 'datetime',
    ];
}
