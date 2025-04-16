<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\WorkProcessSection;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class WorkProcessSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WorkProcessSection::Create([
            'language_id' => getLanguage()->id,
            'style' => 'style1',
            'section_title' => 'SCHEDULE',
            'short_description' => "To arrange any examination, irrespective of the location across the country, please contact our scheduling department at 1(800) 690-9034. Alternatively, you can efficiently schedule your test online through our express scheduling registration. Simply choose your test and fill out the Donor Information/Registration Section. The zip code you input will determine the nearest drug testing center that conducts the selected test. You will receive a donor pass/registration form via email, containing the local testing center's address, operating hours, and instructions. Carry this form with you or keep it accessible on your smartphone to present at the testing center. In most cases, appointments are not required, but you must complete the donor information section and make the payment for the test during registration.",
            'title' => "HOW TO SCHEDULE YOUR TEST",
        ]);
    }
}
