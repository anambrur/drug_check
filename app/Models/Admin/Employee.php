<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_profile_id',
        'user_id',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
