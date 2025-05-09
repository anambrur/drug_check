<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelectionEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'selection_protocol_id',
        'selection_date',
        'pool_size',
        'selection_pool',
        'status'
    ];

    protected $casts = [
        'selection_pool' => 'array',
        'selection_date' => 'datetime'
    ];

    public function protocol()
    {
        return $this->belongsTo(SelectionProtocol::class, 'selection_protocol_id');
    }

    public function selectedEmployees()
    {
        return $this->hasMany(SelectedEmployee::class);
    }
}

