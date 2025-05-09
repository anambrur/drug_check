<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DotAgency extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'dot_agency_name',
        'status'
    ];

    public function clientProfile()
    {
        return $this->hasMany(ClientProfile::class);
    }

    public function SelectionProtocol()
    {
        return $this->hasMany(SelectionProtocol::class);
    }
}
