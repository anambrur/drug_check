<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MRO extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'doctor_name',
        'mro_address',
        'signature',
        'status',
    ];
}
