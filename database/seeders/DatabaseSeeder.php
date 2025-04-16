<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Admin\BackgroundCategory;
use App\Models\Admin\PackageCategory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            LanguageSeeder::class,
            PanelKeywordSeeder::class,
            FrontendKeywordSeeder::class,
            PermissionSeeder::class,
            PageNameSeeder::class,
            PageBuilderSeeder::class,
            MenuSeeder::class,
            SubmenuSeeder::class,
            AboutSeeder::class,
            BannerSeeder::class,
            ServiceCategorySeeder::class,
            ServiceSeeder::class,
            WorkProcessSectionSeeder::class,
            WorkProcessSeeder::class,
            PortfolioCategorySeeder::class,
            PortfolioSeeder::class,
            TestimonialSeeder::class,
            SponsorSeeder::class,
            BackgroundCategorySeeder::class,
            BackgroundSeeder::class,
            PackageCategorySeeder::class,
        ]);

    }
}
