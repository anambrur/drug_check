<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultPanel extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function panel()
    {
        return $this->belongsTo(Panel::class);
    }

    public function resultRecording()
    {
        return $this->belongsTo(ResultRecording::class, 'result_id');
    }
}
