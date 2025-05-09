<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultRecording extends Model
{
    use HasFactory;

    protected $guarded =['id'];


    public function clientProfile()
    {
        return $this->belongsTo(ClientProfile::class,'company_id');
    }


    public function testAdmin()
    {
        return $this->belongsTo(TestAdmin::class);
    }

    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class)->withDefault();
    }

    public function mro()
    {
        return $this->belongsTo(MRO::class)->withDefault();
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function panel()
    {
        return $this->belongsTo(Panel::class);
    }

    public function resultPanel()
    {
        return $this->hasMany(ResultPanel::class ,'result_id');
    }
}