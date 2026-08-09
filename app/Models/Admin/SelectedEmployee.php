<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelectedEmployee extends Model
{
    use HasFactory;

    protected $fillable = [
        'selection_event_id',
        'employee_id',
        'donor_id',
        'test_id',
        'selection_protocol_id',
        'selection_type',
        'random_number',
        'draw_pool',
        'pool_range_max',
        'print_order',
        'status',
        'notification_sent',
        'notification_sent_at',
        'is_excused',
        'is_refused',
        'alternate_replaces_id',
        'replacement_reason',
    ];

    protected $casts = [
        'draw_pool' => 'array',
        'is_excused' => 'boolean',
        'is_refused' => 'boolean',
        'notification_sent' => 'boolean',
        'notification_sent_at' => 'datetime',
    ];

    public function selectionEvent()
    {
        return $this->belongsTo(SelectionEvent::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function company()
    {
        return $this->employee->clientProfile->company_name ?? 'N/A';
    }

    public function test()
    {
        return $this->belongsTo(TestAdmin::class);
    }

    public function alternateReplaces()
    {
        return $this->belongsTo(SelectedEmployee::class, 'alternate_replaces_id');
    }

    public function replacementAlternate()
    {
        return $this->hasOne(SelectedEmployee::class, 'alternate_replaces_id');
    }

    public function resultRecording()
    {
        return $this->hasOne(ResultRecording::class, 'selected_employee_id');
    }

    public function resultRecordings()
    {
        return $this->hasMany(ResultRecording::class, 'selected_employee_id');
    }
}