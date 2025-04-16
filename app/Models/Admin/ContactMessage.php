<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'message',
        'address',
        'preferred_location',
        'employee_name',
        'company_name',
        'accounting_email',
        'test_category',
        'reason_for_testing',
        'date',
        'gender',
        'services',
        'price',
        'read',
    ];
}
