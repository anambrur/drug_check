<?php

namespace Tests\Feature;

use App\Mail\ClearingHouseEnrollmentAdminNotification;
use App\Models\Admin\ClearingHousePlan;
use App\Models\Admin\ClearingHousePlanFee;
use App\Models\ClearingHouseEnrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClearingHousePricingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\LanguageSeeder::class);
    }

    public function test_clearing_house_plan_total_price_calculation(): void
    {
        $plan = ClearingHousePlan::create([
            'name' => 'Test Fleet',
            'slug' => 'test-fleet',
            'min_drivers' => 2,
            'max_drivers' => 10,
            'is_active' => true,
        ]);

        ClearingHousePlanFee::create([
            'clearing_house_plan_id' => $plan->id,
            'fee_key' => 'annual_fee',
            'fee_label' => 'Annual C/TPA Enrollment Fee',
            'fee_amount' => 5000, // $50.00
            'fee_type' => 'flat',
        ]);

        ClearingHousePlanFee::create([
            'clearing_house_plan_id' => $plan->id,
            'fee_key' => 'driver_fee',
            'fee_label' => 'Per-Driver Registration Fee',
            'fee_amount' => 1000, // $10.00
            'fee_type' => 'per_driver',
        ]);

        // Calculate for 5 drivers: 50.00 (flat) + 10.00 * 5 = 100.00 (10000 cents)
        $this->assertEquals(10000, $plan->calculateTotal(5));
        $this->assertEquals(50.00, $plan->getFeeInDollars('annual_fee'));
        $this->assertEquals(10.00, $plan->getFeeInDollars('driver_fee'));
    }

    public function test_unauthenticated_cannot_access_admin_plans(): void
    {
        $response = $this->get(route('admin.clearing-house-plans.index'));
        $response->assertRedirect('/login');
    }

    public function test_clearing_house_plans_seeder_creates_fleet_tiers(): void
    {
        $this->seed(\Database\Seeders\ClearingHousePlansSeeder::class);

        $this->assertEquals(5, ClearingHousePlan::count());
        $this->assertTrue(ClearingHousePlan::where('slug', 'owner-operator')->exists());
        $this->assertGreaterThan(0, ClearingHousePlanFee::count());

        $owner = ClearingHousePlan::where('slug', 'owner-operator')->with('fees')->first();
        $this->assertNotNull($owner);
        $this->assertEquals(1, $owner->min_drivers);
        $this->assertNull($owner->max_drivers); // flexible — any driver count
        // $75 + $25 + $12.50 + $10 = $122.50 => 12250 cents
        $this->assertEquals(12250, $owner->calculateTotal(1));
        // $75 + $25 + $12.50 + ($10 * 3) = $142.50 => 14250 cents
        $this->assertEquals(14250, $owner->calculateTotal(3));
    }

    public function test_admin_notification_email_renders_with_admin_panel_link(): void
    {
        $plan = ClearingHousePlan::create([
            'name' => 'Owner Operator',
            'slug' => 'owner-operator',
            'min_drivers' => 1,
            'max_drivers' => null,
            'is_active' => true,
        ]);

        $enrollment = ClearingHouseEnrollment::create([
            'company_name' => 'Acme Trucking',
            'dot_number' => '1234567',
            'ein_number' => '12-3456789',
            'company_phone' => '5551234567',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'job_title' => 'DER',
            'email' => 'jane@acme-trucking.test',
            'phone' => '5559876543',
            'address_line_1' => '100 Main St',
            'city' => 'Austin',
            'state' => 'TX',
            'zip_code' => '73301',
            'clearinghouse_registered' => 'yes',
            'selected_plan' => $plan->name,
            'driver_count' => 1,
            'amount' => 12250,
            'stripe_payment_intent_id' => 'pi_test_123',
        ]);

        $rendered = (new ClearingHouseEnrollmentAdminNotification($enrollment, $plan))->render();

        $this->assertStringContainsString(
            route('clearing-house-enrollments.show', ['id' => $enrollment->id]),
            $rendered
        );
        $this->assertStringContainsString('Acme Trucking', $rendered);
    }
}
