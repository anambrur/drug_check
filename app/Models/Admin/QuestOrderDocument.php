<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestOrderDocument extends Model
{
    protected $fillable = [
        'quest_order_id',
        'screen_type',
        'doc_type',
        'file_path',
        'file_hash',
        'quest_specimen_id',
        'downloaded_at',
        'downloaded_by',
    ];

    protected $casts = [
        'downloaded_at' => 'datetime',
    ];

    public function questOrder(): BelongsTo
    {
        return $this->belongsTo(QuestOrder::class);
    }

    public function downloadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'downloaded_by');
    }
}
