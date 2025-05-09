<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelectionProtocol extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'client_id',
        'test_id',
        'group',
        'dot_agency_id',
        'department_filter',
        'shift_filter',
        'exclude_previously_selected',
        'selection_requirement_type',
        'selection_requirement_value',
        'selection_period',
        'monthly_selection_day',
        'manual_dates',
        'alternates_type',
        'alternates_value',
        'automatic',
        'calculate_pool_average',
        'is_active'
    ];

    protected $casts = [
        'manual_dates' => 'array',
        'exclude_previously_selected' => 'boolean',
        'automatic' => 'boolean',
        'calculate_pool_average' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function client()
    {
        return $this->belongsTo(ClientProfile::class, );
    }

    public function test()
    {
        return $this->belongsTo(TestAdmin::class, 'test_id');
    }

    public function extraTests()
    {
        return $this->hasMany(ProtocolExtraTest::class, 'selection_protocol_id');
    }

    public function subSelections()
    {
        return $this->hasMany(ProtocolSubSelection::class, 'selection_protocol_id');
    }

    public function selectionEvents()
    {
        return $this->hasMany(SelectionEvent::class);
    }

    public function dotAgency()
    {
        return $this->belongsTo(DotAgency::class, 'dot_agency_id');
    }
}
