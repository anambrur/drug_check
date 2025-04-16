<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'language_id',
        'package_category_id',
        'category_name',
        'title',
        'description',
        'result',
        'price',
        'order',
        'status',
    ];

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function package_category()
    {
        return $this->belongsTo(PackageCategory::class);
    }
}
