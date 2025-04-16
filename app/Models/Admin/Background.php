<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Background extends Model
{
    use HasFactory;

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    // public function sluggable(): array
    // {
    //     return [
    //         'background_slug' => [
    //             'source' => 'title',
    //             'maxLength'          => null,
    //             'maxLengthKeepWords' => true,
    //             'method'             => null,
    //             'separator'          => '-',
    //             'unique'             => true,
    //             'uniqueSuffix'       => null,
    //             'includeTrashed'     => false,
    //             'reserved'           => null,
    //             'onUpdate'           => true
    //         ]
    //     ];
    // }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'language_id',
        'style',
        'section_image',
        'section_title',
        'title',
        'background_slug',
        'breadcrumb_status',
        'custom_breadcrumb_image',
        'custom_breadcrumb_image2',
        'custom_breadcrumb_image3',
        'description',
        'description2',
        'description3',

    ];

    public function background_category()
    {
        return $this->belongsTo('App\Models\Admin\BackgroundCategory','category_id','id');
    }
}
