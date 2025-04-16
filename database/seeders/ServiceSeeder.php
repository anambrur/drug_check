<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use App\Models\Admin\Service;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'language_id' => getLanguage()->id,
                'style' => 'style1',
                'category_name' => 'Drug testing DOT and Non DOT',
                'category_id' => 4,
                'type' => 'icon',
                'icon' => 'fas fa-ambulance',
                'section_image' => '',
                'section_image_2' => '1738661571-slider2.jpg',
                'title' => 'BAT - REGULATED',
                'short_description' => 'BREATH & ETG',
                'service_slug' => Str::slug('bat-regulated'),
                'button_name' => 'Schedule Now',
                'button_url' => '',
                'order' => 0,
                'status' => 'published',
            ],
            [
                'language_id' => getLanguage()->id,
                'style' => 'style1',
                'category_name' => 'Drug testing DOT and Non DOT',
                'category_id' => 4,
                'type' => 'icon',
                'icon' => 'fab fa-algolia',
                'section_image' => '',
                'section_image_2' => '1738661082-slider1.jpg',
                'title' => 'DOT DRUG PANEL W/TS',
                'short_description' => '90 DAY DETECTION',
                'service_slug' => Str::slug('dot-drug-panel-w-ts'),
                'button_name' => 'Schedule Now',
                'button_url' => '',
                'order' => 0,
                'status' => 'published',
            ],
            
        ];

        Service::insert($services);
    }
}
