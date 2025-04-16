<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\Testimonial;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $testimonial = [
            [
                'id' => 1,
                'language_id' => 1,
                'style' => 'style1',
                'section_image' => '1738737226-client2.png',
                'name' => 'NAME, TITLE',
                'job' => 'CEO',
                'description' => 'Thanks to My drug check, our pharmaceutical shipments are now more secure and reliable.',
                'star' => 5,
                'order' => 0,
                'created_at' => '2025-02-05 06:33:46',
                'updated_at' => '2025-02-05 06:33:46',
            ],
            [
                'id' => 2,
                'language_id' => 1,
                'style' => 'style1',
                'section_image' => '1738737318-client4.png',
                'name' => 'NAME, TITLE',
                'job' => 'CEO',
                'description' => 'Thanks to My drug check, our pharmaceutical shipments are now more secure and reliable.',
                'star' => 4,
                'order' => 0,
                'created_at' => '2025-02-05 06:35:19',
                'updated_at' => '2025-02-05 06:35:19',
            ],
        ];
        Testimonial::insert($testimonial);
    }
}
