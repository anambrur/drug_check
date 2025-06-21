<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_profile_id',
        'first_name',
        'last_name',
        'middle_name',
        'department',
        'shift',
        'date_of_birth',
        'start_date',
        'end_date',
        'employee_id',
        'background_check_date',
        'ssn',
        'email',
        'phone',
        'short_description',
        'cdl_state',
        'cdl_number',
        'status',
        'dot',
    ];

    protected $dates = [
        'date_of_birth',
        'start_date',
        'end_date',
        'background_check_date',
    ];

    public function clientProfile()
    {
        return $this->belongsTo(ClientProfile::class, 'client_profile_id');
    }

    public function selections()
    {
        return $this->hasMany(SelectedEmployee::class);
    }
}
