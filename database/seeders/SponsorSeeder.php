<?php

namespace Database\Seeders;

use App\Models\Admin\Sponsor;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SponsorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sponsors = [
            [
                'id' => 1,
                'style' => 'style1',
                'section_image' => '1738739337-patner1.gif',
                'url' => '',
                'order' => 0,
                'created_at' => '2025-02-05 07:08:57',
                'updated_at' => '2025-02-05 07:08:57',
            ],
            [
                'id' => 2,
                'style' => 'style1',
                'section_image' => '1738739357-patner2.png',
                'url' => '',
                'order' => 0,
                'created_at' => '2025-02-05 07:09:17',
                'updated_at' => '2025-02-05 07:09:17',
            ],
            [
                'id' => 3,
                'style' => 'style1',
                'section_image' => '1738739365-patner3.png',
                'url' => '',
                'order' => 0,
                'created_at' => '2025-02-05 07:09:25',
                'updated_at' => '2025-02-05 07:09:25',
            ],
        ];

        Sponsor::insert($sponsors);
    }
}
