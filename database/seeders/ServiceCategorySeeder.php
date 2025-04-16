<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\ServiceCategory;
use Illuminate\Support\Str; // Import the Str class for generating slugs
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $serviceCategories = [
            [
                'language_id' => getLanguage()->id,
                'category_name' => 'Random Consortium',
                'service_category_slug' => Str::slug('Random Consortium'), // Generate slug
                'status' => 1,
                'order' => 0,
                'created_at' => now(), // Add timestamps if needed
                'updated_at' => now(),
            ],
            [
                'language_id' => getLanguage()->id,
                'category_name' => 'Background Checks',
                'service_category_slug' => Str::slug('Background Checks'), // Generate slug
                'status' => 1,
                'order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_id' => getLanguage()->id,
                'category_name' => 'DOT Supervisor Training',
                'service_category_slug' => Str::slug('DOT Supervisor Training'), // Generate slug
                'status' => 1,
                'order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_id' => getLanguage()->id,
                'category_name' => 'Drug testing DOT and Non DOT',
                'service_category_slug' => Str::slug('Drug testing DOT and Non DOT'), // Generate slug
                'status' => 1,
                'order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Use insert() to insert multiple records
        ServiceCategory::insert($serviceCategories);
    }
}