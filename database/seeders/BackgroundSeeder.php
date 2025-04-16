<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\Background;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BackgroundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Background::create(
            [
                'language_id' => 1,
                'style' => 'style1',
                'section_image' => '',
                'section_title' => 'Intellishield Promise',
                'description2' => '<h4><span><span>- Fast, Compliant, and Accurate Reporting<br>- Cloud-based, online portal with Bank-grade security<br>- Best Nationwide overage<br>- No Monthly Feed, No Minimums, No Contracts, Pay as you go Pricing</span></span></h4>',
                'description3' => "",
                'breadcrumb_status' => 'no',
                'custom_breadcrumb_image' => '',
                'custom_breadcrumb_image2' => '',
                'custom_breadcrumb_image3' => '',
                'title' => 'SERVICES',
                'description'=> '',
            ]
        );
    }
}
