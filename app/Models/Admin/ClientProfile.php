<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'profile_id',
        'company_name',
        'description',
        'address',
        'city',
        'state',
        'zip',
        'phone',
        'fax',
        'shipping_address',
        'billing_contact_name',
        'billing_email',
        'billing_phone',
        'der_contact_name',
        'der_contact_email',
        'der_contact_phone',
        'client_start_date',
        'status',
    ];
}
