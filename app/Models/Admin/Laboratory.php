<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laboratory extends Model
{
    use HasFactory;

    protected $fillable = [
        'laboratory_name',
        'laboratory_address',
        'clia_certification',
        'status',
    ];
}
