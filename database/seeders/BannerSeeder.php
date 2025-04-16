<?php

namespace Database\Seeders;

use App\Models\Admin\Banner;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Banner::create([
            'language_id' => getLanguage()->id,
            'style' => 'style3',
            'section_image' => '1738579964-slider11.jpg',
            'section_image_2' => '1738579965-slider22.jpg',
            'section_image_3' => '1738579966-slider33.jpg',
            'title' => 'Drug, Alcohol, Lab &amp; DNA LOCAL TESTING CENTRES',
            'description' => 'Heading 2DrugSafe Navigator Opt for the finest when in need of a test, Same-day   service is at your behest.Nationwide testing centers, providing the best.',
            'youtube_video_url' => '',
            'button_name' => '',
            'button_url' => '',
            'button_name_2' => '',
            'button_url_2' => '',
        ]);
    }
}
