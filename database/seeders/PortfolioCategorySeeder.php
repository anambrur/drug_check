<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Models\Admin\PortfolioCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PortfolioCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $portfolioCategory = [
            [
                'language_id' => getLanguage()->id,
                'category_name' => 'Random Consortium',
                'portfolio_category_slug' => Str::slug('Random Consortium'),
                'status' => 1,
                'order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_id' => getLanguage()->id,
                'category_name' => 'Background Checks',
                'portfolio_category_slug' => Str::slug('Background Checks'),
                'status' => 1,
                'order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_id' => getLanguage()->id,
                'category_name' => 'DOT Supervisor Training',
                'portfolio_category_slug' => Str::slug('DOT Supervisor Training'),
                'status' => 1,
                'order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_id' => getLanguage()->id,
                'category_name' => 'DOT Testing',
                'portfolio_category_slug' => Str::slug('DOT Testing'),
                'status' => 1,
                'order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_id' => getLanguage()->id,
                'category_name' => 'Non DOT Testing',
                'portfolio_category_slug' => Str::slug('Non DOT Testing'),
                'status' => 1,
                'order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        PortfolioCategory::insert($portfolioCategory);
    }
}
