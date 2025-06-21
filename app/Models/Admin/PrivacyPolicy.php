<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivacyPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'breadcrumb_status',
        'custom_breadcrumb_image',
        'language_id',
        'style',
    ];
}
