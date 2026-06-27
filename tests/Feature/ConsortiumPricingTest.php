<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Admin\ConsortiumPlan;
use App\Models\Admin\ConsortiumPlanFee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsortiumPricingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\LanguageSeeder::class);
    }

    public function test_consortium_plan_total_price_calculation(): void
    {
        $plan = ConsortiumPlan::create([
            'name' => 'Test Fleet',
            'slug' => 'test-fleet',
            'min_drivers' => 2,
            'max_drivers' => 10,
            'is_active' => true,
        ]);

        ConsortiumPlanFee::create([
            'consortium_plan_id' => $plan->id,
            'fee_key' => 'annual_fee',
            'fee_label' => 'Annual Fee',
            'fee_amount' => 5000, // $50.00
            'fee_type' => 'flat',
        ]);

        ConsortiumPlanFee::create([
            'consortium_plan_id' => $plan->id,
            'fee_key' => 'driver_fee',
            'fee_label' => 'Driver Fee',
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
        $response = $this->get(route('admin.consortium-plans.index'));
        $response->assertRedirect('/login');
    }

    public function test_authenticated_admin_can_access_admin_plans(): void
    {
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $user = User::where('email', 'admin@admin.com')->first();
        if (!$user) {
            $user = User::factory()->create();
            $user->assignRole('super-admin');
        }

        $response = $this->actingAs($user)->get(route('admin.consortium-plans.index'));
        $response->assertStatus(200);
    }
}
