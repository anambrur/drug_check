<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\PackageCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PackageCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packageCategories = [
            [
                'language_id' => 1,
                'category_name' => 'RECOMMENDED PACKAGES',
                'status' => 1,
                'order' => 1,
                'package_category_slug' => 'recommended-packages',
            ],
            [
                'language_id' => 1,
                'category_name' => 'CRIMINAL RECORDS SERVICES',
                'status' => 1,
                'order' => 2,
                'package_category_slug' => 'criminal-records-services',
            ],
            [
                'language_id' => 1,
                'category_name' => 'EMPLOYMENT / VOLUNTEER VERIFICATION SERVICES',
                'status' => 1,
                'order' => 3,
                'package_category_slug' => 'employment-volunteer-verification-services',
            ],
            [
                'language_id' => 1,
                'category_name' => 'IDENTITY BASED VERIFICATIONS',
                'status' => 1,
                'order' => 4,
                'package_category_slug' => 'identity-based-verifications',
            ],
            [
                'language_id' => 1,
                'category_name' => 'INTERNATIONAL APPLICANT SERVICES',
                'status' => 1,
                'order' => 5,
                'package_category_slug' => 'international-applicant-services',
            ],
            [
                'language_id' => 1,
                'category_name' => 'SPECIALTY PRODUCTS AND SERVICES',
                'status' => 1,
                'order' => 6,
                'package_category_slug' => 'specialty-products-and-services',
            ],
        ];

        PackageCategory::insert($packageCategories);
    }
}
