<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_id',
        'profile_id',
        'company_name',
        'short_description',
        'address',
        'city',
        'state',
        'zip',
        'phone',
        'fax',
        'dot_agency_id',
        'shipping_address',
        'billing_contact_name',
        'billing_contact_email',
        'billing_contact_phone',
        'der_contact_name',
        'der_contact_email',
        'der_contact_phone',
        'client_start_date',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function selectionProtocols()
    {
        return $this->hasMany(SelectionProtocol::class);
    }

    public function dotAgency()
    {
        return $this->belongsTo(DotAgency::class);
    }
}
