<?php

namespace Database\Seeders;

use App\Models\Admin\Submenu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubmenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create data
        Submenu::insert([
            [
                'language_id' => 1,
                'menu_id' => 3,
                'menu_name' => 'Services',
                'submenu_name' => 'Services',
                'uri' => 'services',
                'url' => null,
                'view' => 0,
                'status' => 'published',
                'order' => 0,
            ],

            [
                'language_id' => 1,
                'menu_id' => 7,
                'menu_name' => 'Background Checks',
                'submenu_name' => 'Background Checks',
                'uri' => 'background-checks',
                'url' => null,
                'view' => 0,
                'status' => 'published',
                'order' => 0,
            ],
            
            [
                'language_id' => 1,
                'menu_id' => 7,
                'menu_name' => 'Background Checks',
                'submenu_name' => 'Forms',
                'uri' => 'background-checks-forms',
                'url' => null,
                'view' => 0,
                'status' => 'published',
                'order' => 0,
            ],
            
            [
                'language_id' => 1,
                'menu_id' => 7,
                'menu_name' => 'Background Checks',
                'submenu_name' => 'Services',
                'uri' => 'background-checks-services',
                'url' => null,
                'view' => 0,
                'status' => 'published',
                'order' => 0,
            ],
            [
                'language_id' => 1,
                'menu_id' => 7,
                'menu_name' => 'Background Checks',
                'submenu_name' => 'Terms and Conditions',
                'uri' => 'terms-and-conditions',
                'url' => null,
                'view' => 0,
                'status' => 'published',
                'order' => 0,
            ],
        ]);
    }
}
