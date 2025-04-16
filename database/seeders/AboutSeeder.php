<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\AboutSection;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AboutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AboutSection::firstOrCreate([
            'language_id' => getLanguage()->id,
            'style' => 'style1',
            'section_image' => '1738566219-Science Lab.png',
            'video_type' => 'youtube',
            'video_url' => '',
            'section_title' => "About Us",
            'title' => "Why Choose My Drug Checks",
                    'description' => "My Drug Check stands as one of the nation's foremost providers of drug testing, alcohol testing, and DNA testing services catering to employers, courts, government agencies, and individuals. Our extensive network of drug testing centers spans across all states and cities. Our facilities conduct a variety of drug screenings, including 5, 10, 12, and 14-panel tests, offering instant results through FDA-approved products and utilizing SAMHSA Certified Laboratories.

                    Our comprehensive drug testing methods encompass urine, hair, blood, and fingernail analyses. We also offer direct-to-lab testing services. Our testing services cater to various purposes, including employment screening, court-ordered tests, probation monitoring, post-accident assessments, reasonable suspicion testing, return-to-duty evaluations, and family-related testing. Additionally, we offer mobile drug and alcohol testing services for construction sites, warehouses, schools, and employers of all sizes.

                    For DOT-regulated companies, we provide specialized services, such as DOT drug testing, DOT breath alcohol testing, DOT consortium enrollment, FMCSA Clearinghouse services, and DOT supervisor training. Accredited Drug Testing boasts over 20,000 testing locations, ensuring we are well-equipped to meet your testing needs with a commitment to friendliness, confidentiality, and convenience.",
            'button_name' => 'about-us',
            'button_url' => 'about',
            'button_name_2' => '',
            'cv_file' => '',
        ]);
    }
}
