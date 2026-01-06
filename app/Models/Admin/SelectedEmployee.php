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
        'test_id',
        'selection_protocol_id',
        'selection_type',
        'random_number',
        'is_excused',
        'is_refused',
        'alternate_replaces_id'
    ];

    protected $casts = [
        'is_excused' => 'boolean',
        'is_refused' => 'boolean'
    ];

    public function selectionEvent()
    {
        return $this->belongsTo(SelectionEvent::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
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

    public function resultRecordings()
{
    return $this->hasMany(ResultRecording::class, 'employee_id', 'employee_id');
}
}