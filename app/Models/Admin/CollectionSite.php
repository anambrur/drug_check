<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class CollectionSite extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'last_updated' => 'date',
    ];
}
