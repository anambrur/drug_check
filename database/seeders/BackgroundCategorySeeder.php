<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\BackgroundCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BackgroundCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $backgroundCategories = [
            [
                'language_id' => 1,
                'category_name' => 'Criminal Records',
                'status' => 1,
                'order' => 1,
                'short_description' => 'National criminal multi jurisdictional, Real-time County, Statewide, Federal Court searches + Sex Offender, and warrants.',
                'background_category_slug' => 'criminal-records',
            ],
            [
                'language_id' => 1,
                'category_name' => 'Identity Checks',
                'status' => 1,
                'order' => 1,
                'short_description' => 'Verify Social Security Number with names, aliases, date of birth, and address history.',
                'background_category_slug' => 'identity-checks',
            ],
            [
                'language_id' => 1,
                'category_name' => 'Verifications',
                'status' => 1,
                'order' => 1,
                'short_description' => 'Employment history, education history,professional license, personal & professional references checks',
                'background_category_slug' => 'verifications',
            ],
            [
                'language_id' => 1,
                'category_name' => 'Drug Testing',
                'status' => 1,
                'order' => 1,
                'short_description' => 'Instant & Lab-based Drug & Alcohol Testing + Health Screening Services',
                'background_category_slug' => 'drug-testing',
            ],
            [
                'language_id' => 1,
                'category_name' => 'Watch-list & Sanctions',
                'status' => 1,
                'order' => 1,
                'short_description' => 'FBI Most Wanted & Terrorist Watch List, Federal, State, & Local Wanted Fugitives, Federal & International Sanction Lists',
                'background_category_slug' => 'watch-list-sanctions',
            ],
            [
                'language_id' => 1,
                'category_name' => 'Driver History',
                'status' => 1,
                'order' => 1,
                'short_description' => 'MVR Reports provide - license class, status, accident history, and citations, + CDLIS, PSP Reports available',
                'background_category_slug' => 'driver-history',
            ],
            [
                'language_id' => 1,
                'category_name' => 'Civil Records',
                'status' => 1,
                'order' => 1,
                'short_description' => 'Federal & County Civil Court Records Include - bankruptcy, judgements, suits, claims, Liens & foreclosures',
                'background_category_slug' => 'civil-records',
            ],
            [
                'language_id' => 1,
                'category_name' => 'Criminal Monitoring',
                'status' => 1,
                'order' => 1,
                'short_description' => 'Real-time, continuous monitoring solution for arrests, incarcerations and bookings to mitigate risk.',
                'background_category_slug' => 'criminal-monitoring',
            ],
            [
                'language_id' => 1,
                'category_name' => 'DOT Background Checks',
                'status' => 1,
                'order' => 1,
                'short_description' => 'Includes - Criminal background check, previous employment history, drug testing history, MVR, & a DOT drug test',
                'background_category_slug' => 'dot-background-checks',
            ],
        ];


        BackgroundCategory::insert($backgroundCategories);
    }
}
