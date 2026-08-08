<?php

namespace Tests\Feature\Admin;

use App\Models\Admin\Portfolio;
use App\Models\PortfolioTestApplication;
use App\Models\User;
use App\Services\QuestOrderSubmissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Mockery;
use Mockery\MockInterface;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AdminQuestResubmitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\LanguageSeeder::class);
    }

    public function test_admin_can_resubmit_a_paid_application_that_failed_on_quest(): void
    {
        $application = $this->makeApplication([
            'payment_status' => 'completed',
            'quest_submission_status' => 'failed',
            'quest_submission_error' => 'Quest Diagnostics returned HTTP 400.',
        ]);

        $this->mock(QuestOrderSubmissionService::class, function (MockInterface $mock) use ($application) {
            $mock->shouldReceive('submitFromApplication')
                ->once()
                ->andReturnUsing(function (PortfolioTestApplication $passed) use ($application) {
                    $this->assertSame($application->id, $passed->id);
                    $this->assertSame('pending', $passed->quest_submission_status);
                    $this->assertNull($passed->quest_submission_error);

                    return ['success' => true, 'quest_order_id' => '15670174'];
                });
        });

        $this->actingAs($this->adminUser())
            ->post(route('admin.orders.applications.resubmit', $application->id))
            ->assertRedirect(route('admin.orders.applications.show', $application->id));
    }

    public function test_resubmit_is_refused_when_payment_is_not_completed(): void
    {
        $application = $this->makeApplication([
            'payment_status' => 'pending',
            'quest_submission_status' => 'failed',
        ]);

        $this->mock(QuestOrderSubmissionService::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('submitFromApplication');
        });

        $this->actingAs($this->adminUser())
            ->post(route('admin.orders.applications.resubmit', $application->id))
            ->assertRedirect(route('admin.orders.applications.show', $application->id));

        $this->assertSame('failed', $application->fresh()->quest_submission_status);
    }

    public function test_already_submitted_application_is_not_sent_again(): void
    {
        $application = $this->makeApplication([
            'payment_status' => 'completed',
            'quest_submission_status' => 'submitted',
            'quest_order_id' => '15670174',
        ]);

        $this->mock(QuestOrderSubmissionService::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('submitFromApplication');
        });

        $this->actingAs($this->adminUser())
            ->post(route('admin.orders.applications.resubmit', $application->id))
            ->assertRedirect(route('admin.orders.applications.show', $application->id));
    }

    public function test_user_without_the_quest_order_edit_permission_is_forbidden(): void
    {
        $application = $this->makeApplication([
            'payment_status' => 'completed',
            'quest_submission_status' => 'failed',
        ]);

        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($user)
            ->post(route('admin.orders.applications.resubmit', $application->id))
            ->assertForbidden();
    }

    private function adminUser(): User
    {
        Permission::findOrCreate('quest-order edit', 'web');

        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->givePermissionTo('quest-order edit');

        return $user;
    }

    private function makeApplication(array $attributes): PortfolioTestApplication
    {
        $owner = User::factory()->create(['email_verified_at' => now()]);

        $portfolio = Portfolio::create([
            'language_id' => DB::table('languages')->value('id'),
            'category_id' => 1,
            'category_name' => 'Drug Testing',
            'title' => 'Express Results 10 panel',
            'portfolio_slug' => 'express-results-10-panel',
            'code' => 'ERO10A',
            'price' => 9900,
        ]);

        return PortfolioTestApplication::create(array_merge([
            'test_type' => 'non_dot',
            'portfolio_id' => $portfolio->id,
            'user_id' => $owner->id,
            'first_name' => 'Eleanor',
            'last_name' => 'Doe',
            'email' => 'eleanor@example.com',
            'amount' => 9900,
        ], $attributes));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
