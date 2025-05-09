<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestAdmin extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_name',
        'specimen',
        'method',
        'regulation',
        'description',
        'laboratory_id',
        'mro_id',
        'panel_id',
        'status',
    ];

    public function panel()
    {
        return $this->belongsToMany(Panel::class, 'panel_test_admin');
    }

    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class)->withDefault();
    }

    public function mro()
    {
        return $this->belongsTo(MRO::class)->withDefault();
    }

    public function selectionProtocols()
    {
        return $this->hasMany(SelectionProtocol::class);
    }
}