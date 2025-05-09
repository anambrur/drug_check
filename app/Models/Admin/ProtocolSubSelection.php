<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProtocolSubSelection extends Model
{
    use HasFactory;

    protected $fillable = [
        'selection_protocol_id',
        'test_id',
        'requirement_type',
        'requirement_value'
    ];

    public function protocol()
    {
        return $this->belongsTo(SelectionProtocol::class, 'selection_protocol_id');
    }

    public function test()
    {
        return $this->belongsTo(TestAdmin::class);
    }
}
