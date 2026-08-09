<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelectionOfflineList extends Model
{
    use HasFactory;

    protected $fillable = [
        'selection_event_id',
        'selection_protocol_id',
        'shuffled_donor_ids',
        'cursor',
        'is_single_use',
        'printed_at',
    ];

    protected $casts = [
        'shuffled_donor_ids' => 'array',
        'is_single_use' => 'boolean',
        'printed_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(SelectionEvent::class, 'selection_event_id');
    }

    public function protocol()
    {
        return $this->belongsTo(SelectionProtocol::class, 'selection_protocol_id');
    }

    public function consumptions()
    {
        return $this->hasMany(SelectionOfflineListConsumption::class);
    }

    public function remainingCount(): int
    {
        $total = count($this->shuffled_donor_ids ?? []);

        return max(0, $total - (int) $this->cursor);
    }
}
