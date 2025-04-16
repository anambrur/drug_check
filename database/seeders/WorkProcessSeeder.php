<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\WorkProcess;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class WorkProcessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $workProcess = [
            [
                'language_id' => getLanguage()->id,
                'style' => 'style1',
                'section_image' => '',
                'title' => 'SCHEDULE YOUR TEST',
                'short_description' => 'Choose and buy your test on our website or contact our scheduling center at 1(800) 690-9034, where our representatives will be happy to help you. Payment is required at the time of registration, as testing centers do not handle payments. Same-day service is an option for your convenience.',
                'order' => 0,
            ],
            [
                'language_id' => getLanguage()->id,
                'style' => 'style1',
                'section_image' => '',
                'title' => 'FILL OUT THE FORM',
                'short_description' => "Once you've made the purchase for your test, fill out the Donor Information/Registration Section, specifying the email address where you want the confidential test results to be sent. The zip code you provide will be used to identify the nearest drug/alcohol testing center, where you will need to go to undergo your test.",
                'order' => 0,
            ],
            [
                'language_id' => getLanguage()->id,
                'style' => 'style1',
                'section_image' => '',
                'title' => 'TAKE YOUR TEST',
                'short_description' => "The registration pass, containing the local testing center's address and operating hours, will be sent to the email address you have provided. Ensure you have the form with you or accessible on your smartphone to present it at the testing center. No appointment is required; simply proceed to the testing center with the necessary documentation.",
                'order' => 0,
            ],
        ];

        WorkProcess::insert($workProcess);
    }
}
