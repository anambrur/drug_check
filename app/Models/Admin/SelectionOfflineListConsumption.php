<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelectionOfflineListConsumption extends Model
{
    use HasFactory;

    protected $fillable = [
        'selection_offline_list_id',
        'list_index',
        'donor_id',
        'employee_id',
        'selected_employee_id',
        'replaces_selected_employee_id',
        'consumed_at',
    ];

    protected $casts = [
        'consumed_at' => 'datetime',
    ];

    public function offlineList()
    {
        return $this->belongsTo(SelectionOfflineList::class, 'selection_offline_list_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function selectedEmployee()
    {
        return $this->belongsTo(SelectedEmployee::class, 'selected_employee_id');
    }

    public function replacesSelectedEmployee()
    {
        return $this->belongsTo(SelectedEmployee::class, 'replaces_selected_employee_id');
    }
}
