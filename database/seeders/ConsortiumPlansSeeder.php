<?php

namespace Database\Seeders;

use App\Models\Admin\ConsortiumPlan;
use App\Models\Admin\ConsortiumPlanFee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ConsortiumPlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Owner Operator',
                'min_drivers' => 1,
                'max_drivers' => 1,
                'display_order' => 1,
                'is_active' => true,
                'description' => 'Perfect for individual owner-operators with a single driver.',
            ],
            [
                'name' => 'Small Fleet',
                'min_drivers' => 2,
                'max_drivers' => 10,
                'display_order' => 2,
                'is_active' => true,
                'description' => 'Designed for small fleets managing 2 to 10 drivers.',
            ],
            [
                'name' => 'Medium Fleet',
                'min_drivers' => 11,
                'max_drivers' => 50,
                'display_order' => 3,
                'is_active' => true,
                'description' => 'For growing businesses managing 11 to 50 drivers.',
            ],
            [
                'name' => 'Large Fleet',
                'min_drivers' => 51,
                'max_drivers' => 100,
                'display_order' => 4,
                'is_active' => true,
                'description' => 'Designed for large fleets managing 51 to 100 drivers.',
            ],
            [
                'name' => 'Enterprise Fleet',
                'min_drivers' => 101,
                'max_drivers' => null,
                'display_order' => 5,
                // We keep it active but since it's custom, let's seed it as active or inactive
                // Actually, the user can configure it or we can keep it active and customize pricing logic
                'is_active' => true,
                'description' => 'Custom pricing and features for fleets with more than 100 drivers.',
            ],
        ];

        $standardFees = [
            [
                'fee_key' => 'annual_enrollment_fee',
                'fee_label' => 'Annual Enrollment Fee',
                'fee_amount' => 7500, // $75.00
                'fee_type' => 'flat',
                'display_order' => 1,
            ],
            [
                'fee_key' => 'clearinghouse_maintenance_fee',
                'fee_label' => 'Clearinghouse Maintenance Fee',
                'fee_amount' => 2500, // $25.00
                'fee_type' => 'flat',
                'display_order' => 2,
            ],
            [
                'fee_key' => 'fmcsa_queries_fee',
                'fee_label' => 'FMCSA Queries Fee',
                'fee_amount' => 1250, // $12.50
                'fee_type' => 'flat',
                'display_order' => 3,
            ],
            [
                'fee_key' => 'per_driver_fee',
                'fee_label' => 'Per-Driver Registration Fee',
                'fee_amount' => 1000, // $10.00
                'fee_type' => 'per_driver',
                'display_order' => 4,
            ],
        ];

        foreach ($plans as $planData) {
            $plan = ConsortiumPlan::updateOrCreate(
                ['slug' => Str::slug($planData['name'])],
                [
                    'name' => $planData['name'],
                    'min_drivers' => $planData['min_drivers'],
                    'max_drivers' => $planData['max_drivers'],
                    'display_order' => $planData['display_order'],
                    'is_active' => $planData['is_active'],
                    'description' => $planData['description'],
                ]
            );

            // Seed fees for this plan
            foreach ($standardFees as $feeData) {
                ConsortiumPlanFee::updateOrCreate(
                    [
                        'consortium_plan_id' => $plan->id,
                        'fee_key' => $feeData['fee_key'],
                    ],
                    [
                        'fee_label' => $feeData['fee_label'],
                        'fee_amount' => $feeData['fee_amount'],
                        'fee_type' => $feeData['fee_type'],
                        'display_order' => $feeData['display_order'],
                    ]
                );
            }
        }
    }
}
