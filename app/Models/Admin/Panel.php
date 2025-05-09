<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Panel extends Model
{
    use HasFactory;

    protected $fillable = [
        'drug_name',
        'drug_code',
        'cut_off_level',
        'conf_level',
        'status',
    ];

    public function testAdmins()
    {
        return $this->belongsToMany(TestAdmin::class, 'panel_test_admin');
    }
}
