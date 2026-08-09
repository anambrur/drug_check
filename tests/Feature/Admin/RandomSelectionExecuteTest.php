<?php

namespace Tests\Feature\Admin;

use App\Models\Admin\ClientProfile;
use App\Models\Admin\DotAgency;
use App\Models\Admin\Employee;
use App\Models\Admin\ResultRecording;
use App\Models\Admin\SelectedEmployee;
use App\Models\Admin\SelectionEvent;
use App\Models\Admin\SelectionOfflineList;
use App\Models\Admin\SelectionProtocol;
use App\Models\Admin\TestAdmin;
use App\Models\User;
use App\Services\RandomSelection\RandomSelectionSchedule;
use App\Services\RandomSelectionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class RandomSelectionExecuteTest extends TestCase
{
    use RefreshDatabase;

    protected function createProtocolContext(int $employeeCount = 5, array $protocolOverrides = []): array
    {
        $user = User::factory()->create();
        $agency = DotAgency::create([
            'full_name' => 'Federal Motor Carrier Safety Administration',
            'dot_agency_name' => 'FMCSA',
            'status' => 'active',
        ]);

        $client = ClientProfile::create([
            'user_id' => $user->id,
            'company_name' => 'Acme Transport',
            'address' => '1 Main St',
            'city' => 'Austin',
            'state' => 'TX',
            'zip' => '78701',
            'dot_agency_id' => $agency->id,
            'der_contact_name' => 'Der Contact',
            'der_contact_email' => 'der@acme.test',
            'status' => 'active',
        ]);

        $test = TestAdmin::create([
            'test_name' => 'DOT 5 Panel',
            'status' => 'active',
        ]);

        for ($i = 1; $i <= $employeeCount; $i++) {
            Employee::create([
                'client_profile_id' => $client->id,
                'first_name' => 'Driver',
                'last_name' => 'Number' . $i,
                'employee_id' => 'E' . str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                'email' => "driver{$i}@acme.test",
                'status' => 'active',
                'dot' => 'yes',
            ]);
        }

        $protocol = SelectionProtocol::create(array_merge([
            'name' => 'DOT Monthly',
            'client_id' => $client->id,
            'test_id' => $test->id,
            'group' => 'DOT',
            'dot_agency_id' => $agency->id,
            'exclude_previously_selected' => false,
            'selection_requirement_type' => 'NUMBER',
            'selection_requirement_value' => 2,
            'selection_period' => 'MONTHLY',
            'monthly_selection_day' => 8,
            'alternates_value' => 1,
            'alternates_type' => 'NUMBER',
            'alternate_mode' => RandomSelectionService::ALTERNATE_MODE_IMMEDIATE,
            'randomize_alternate_print_order' => true,
            'automatic' => true,
            'calculate_pool_average' => false,
            'is_active' => true,
            'is_email_send' => false,
        ], $protocolOverrides));

        $protocol->clients()->attach([$client->id]);

        return compact('user', 'agency', 'client', 'test', 'protocol');
    }

    public function test_execute_creates_selections_and_linked_result_recordings(): void
    {
        $context = $this->createProtocolContext(5);
        /** @var SelectionProtocol $protocol */
        $protocol = $context['protocol'];

        $service = app(RandomSelectionService::class);
        $results = $service->executeProtocol($protocol->fresh(['clients', 'extraTests', 'subSelections', 'test']), 'manual');

        $this->assertSame(2, $results['primary']->count());
        $this->assertSame(1, $results['alternates']->count());
        $this->assertDatabaseCount('selection_events', 1);
        $this->assertDatabaseHas('selection_events', [
            'selection_protocol_id' => $protocol->id,
            'status' => 'COMPLETED',
            'trigger' => 'manual',
        ]);

        $this->assertGreaterThanOrEqual(3, SelectedEmployee::count());
        $this->assertGreaterThanOrEqual(3, ResultRecording::count());

        $recording = ResultRecording::first();
        $this->assertNotNull($recording->selection_event_id);
        $this->assertNotNull($recording->selected_employee_id);
        $this->assertSame('Random Selection', $recording->reason_for_test);
    }

    public function test_execute_stores_donor_id_audit_pool_on_each_selection(): void
    {
        $context = $this->createProtocolContext(5);
        $protocol = $context['protocol'];

        $results = app(RandomSelectionService::class)->executeProtocol(
            $protocol->fresh(['clients', 'extraTests', 'subSelections', 'test']),
            'manual'
        );

        $event = $results['event'];
        $this->assertNotEmpty($event->selection_pool);
        $this->assertContainsOnly('string', $event->selection_pool);

        foreach ($event->selectedEmployees as $selection) {
            $this->assertNotEmpty($selection->donor_id);
            $this->assertIsArray($selection->draw_pool);
            $this->assertNotEmpty($selection->draw_pool);
            $this->assertSame(count($selection->draw_pool) - 1, $selection->pool_range_max);
            $this->assertArrayHasKey($selection->random_number, $selection->draw_pool);
            $this->assertSame(
                $selection->donor_id,
                (string) $selection->draw_pool[$selection->random_number]
            );
        }

        $alternate = $results['alternates']->first();
        $this->assertNotNull($alternate->print_order);
    }

    public function test_duplicate_donor_ids_fail_before_draw(): void
    {
        $context = $this->createProtocolContext(2);
        $protocol = $context['protocol'];

        Employee::create([
            'client_profile_id' => $context['client']->id,
            'first_name' => 'Dup',
            'last_name' => 'Driver',
            'employee_id' => 'E0001',
            'email' => 'dup@acme.test',
            'status' => 'active',
            'dot' => 'yes',
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Duplicate DonorIDs');

        app(RandomSelectionService::class)->executeProtocol(
            $protocol->fresh(['clients']),
            'manual'
        );
    }

    public function test_on_demand_alternate_prefers_same_company(): void
    {
        $context = $this->createProtocolContext(3, [
            'alternates_value' => 0,
            'alternate_mode' => RandomSelectionService::ALTERNATE_MODE_ON_DEMAND,
        ]);

        $user2 = User::factory()->create();
        $client2 = ClientProfile::create([
            'user_id' => $user2->id,
            'company_name' => 'Other Co',
            'address' => '2 Main St',
            'city' => 'Dallas',
            'state' => 'TX',
            'zip' => '75201',
            'dot_agency_id' => $context['agency']->id,
            'der_contact_name' => 'Der Two',
            'der_contact_email' => 'der2@other.test',
            'status' => 'active',
        ]);

        Employee::create([
            'client_profile_id' => $client2->id,
            'first_name' => 'Other',
            'last_name' => 'Driver',
            'employee_id' => 'E0099',
            'email' => 'other@other.test',
            'status' => 'active',
            'dot' => 'yes',
        ]);

        $protocol = $context['protocol'];
        $protocol->clients()->attach([$client2->id]);

        $service = app(RandomSelectionService::class);
        $results = $service->executeProtocol(
            $protocol->fresh(['clients', 'extraTests', 'subSelections', 'test']),
            'manual'
        );

        $this->assertSame(0, $results['alternates']->count());
        $this->assertNull($results['offline_list']);

        $primary = $results['primary']->first();
        $alternate = $service->markExcusedOrRefused($primary->fresh(['employee', 'selectionEvent.protocol.clients']), 'excused');

        $this->assertSame('ALTERNATE', $alternate->selection_type);
        $this->assertSame($primary->id, $alternate->alternate_replaces_id);
        $this->assertSame('excused', $alternate->replacement_reason);
        $this->assertTrue($primary->fresh()->is_excused);
        $this->assertSame(
            $primary->employee->client_profile_id,
            $alternate->employee->client_profile_id
        );
    }

    public function test_offline_list_is_permutation_and_consumable(): void
    {
        $context = $this->createProtocolContext(4, [
            'alternates_value' => 0,
            'alternate_mode' => RandomSelectionService::ALTERNATE_MODE_OFFLINE_LIST,
            'selection_requirement_value' => 1,
        ]);

        $service = app(RandomSelectionService::class);
        $results = $service->executeProtocol(
            $context['protocol']->fresh(['clients', 'extraTests', 'subSelections', 'test']),
            'manual'
        );

        /** @var SelectionOfflineList $list */
        $list = $results['offline_list'];
        $this->assertNotNull($list);
        $this->assertCount(4, $list->shuffled_donor_ids);

        $expected = Employee::query()
            ->where('client_profile_id', $context['client']->id)
            ->pluck('employee_id')
            ->map(fn ($id) => (string) $id)
            ->sort()
            ->values()
            ->all();

        $actual = collect($list->shuffled_donor_ids)->map(fn ($id) => (string) $id)->sort()->values()->all();
        $this->assertSame($expected, $actual);

        $primary = $results['primary']->first();
        $consumed = $service->consumeNextFromOfflineList($list->fresh(), $primary->fresh());

        $this->assertSame('ALTERNATE', $consumed->selection_type);
        $this->assertSame($primary->id, $consumed->alternate_replaces_id);
        $this->assertSame($consumed->random_number + 1, $list->fresh()->cursor);
        $this->assertNotSame($primary->employee_id, $consumed->employee_id);
        $this->assertDatabaseHas('selection_offline_list_consumptions', [
            'selection_offline_list_id' => $list->id,
            'selected_employee_id' => $consumed->id,
            'donor_id' => $consumed->donor_id,
            'list_index' => $consumed->random_number,
        ]);
    }

    public function test_inactive_protocol_cannot_execute(): void
    {
        $context = $this->createProtocolContext(3);
        $protocol = $context['protocol'];
        $protocol->update(['is_active' => false]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('inactive');

        app(RandomSelectionService::class)->executeProtocol($protocol->fresh(['clients']), 'manual');
    }

    public function test_manual_dates_round_trip_without_double_encoding(): void
    {
        $context = $this->createProtocolContext(2);
        $protocol = $context['protocol'];

        $protocol->update([
            'selection_period' => 'MANUAL',
            'manual_dates' => ['2026-06-01', '2026-09-15'],
            'monthly_selection_day' => null,
        ]);

        $fresh = $protocol->fresh();
        $this->assertIsArray($fresh->manual_dates);
        $this->assertSame(['2026-06-01', '2026-09-15'], $fresh->manual_dates);
    }

    public function test_same_day_run_skips_due_schedule(): void
    {
        $context = $this->createProtocolContext(3);
        $protocol = $context['protocol'];
        $date = Carbon::parse('2026-08-08');

        SelectionEvent::create([
            'selection_protocol_id' => $protocol->id,
            'selection_date' => $date->copy()->setTime(9, 0),
            'pool_size' => 3,
            'selection_pool' => ['E0001', 'E0002', 'E0003'],
            'status' => 'COMPLETED',
            'trigger' => 'manual',
        ]);

        $schedule = app(RandomSelectionSchedule::class);
        $this->assertFalse($schedule->isDueToday($protocol->fresh(), $date));
    }

    public function test_command_skips_non_automatic_protocols(): void
    {
        $context = $this->createProtocolContext(3);
        $protocol = $context['protocol'];
        $protocol->update([
            'automatic' => false,
            'selection_period' => 'YEARLY',
        ]);

        Artisan::call('random-selection:run-due', [
            '--date' => '2026-01-01',
            '--dry-run' => true,
        ]);

        $this->assertStringContainsString('No protocols due', Artisan::output());
    }

    public function test_command_lists_due_automatic_protocol(): void
    {
        $context = $this->createProtocolContext(3);
        $protocol = $context['protocol'];
        $protocol->update([
            'automatic' => true,
            'is_active' => true,
            'selection_period' => 'MONTHLY',
            'monthly_selection_day' => 8,
        ]);

        Artisan::call('random-selection:run-due', [
            '--date' => '2026-08-08',
            '--dry-run' => true,
        ]);

        $output = Artisan::output();
        $this->assertStringContainsString('1 protocol(s) due', $output);
        $this->assertStringContainsString($protocol->name, $output);
    }

    public function test_current_pool_size_uses_all_protocol_clients(): void
    {
        $context = $this->createProtocolContext(3);
        $user2 = User::factory()->create();
        $client2 = ClientProfile::create([
            'user_id' => $user2->id,
            'company_name' => 'Second Co',
            'address' => '2 Main St',
            'city' => 'Dallas',
            'state' => 'TX',
            'zip' => '75201',
            'dot_agency_id' => $context['agency']->id,
            'der_contact_name' => 'Der Two',
            'der_contact_email' => 'der2@second.test',
            'status' => 'active',
        ]);

        Employee::create([
            'client_profile_id' => $client2->id,
            'first_name' => 'Other',
            'last_name' => 'Driver',
            'employee_id' => 'E0099',
            'email' => 'other@second.test',
            'status' => 'active',
            'dot' => 'yes',
        ]);

        $protocol = $context['protocol'];
        $protocol->clients()->attach([$client2->id]);

        $size = app(RandomSelectionService::class)->currentPoolSize($protocol->fresh(['clients']));
        $this->assertSame(4, $size);
    }
}
