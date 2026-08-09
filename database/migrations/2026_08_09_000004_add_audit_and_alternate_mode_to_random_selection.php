<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('selection_protocols', 'alternate_mode')) {
            Schema::table('selection_protocols', function (Blueprint $table) {
                $table->string('alternate_mode', 32)->default('immediate')->after('alternates_value');
                $table->boolean('randomize_alternate_print_order')->default(true)->after('alternate_mode');
            });
        }

        if (!Schema::hasColumn('selected_employees', 'donor_id')) {
            Schema::table('selected_employees', function (Blueprint $table) {
                $table->string('donor_id')->nullable()->after('employee_id');
                $table->json('draw_pool')->nullable()->after('random_number');
                $table->unsignedInteger('pool_range_max')->nullable()->after('draw_pool');
                $table->unsignedInteger('print_order')->nullable()->after('pool_range_max');
                $table->string('replacement_reason', 32)->nullable()->after('alternate_replaces_id');
            });
        }

        if (!Schema::hasTable('selection_offline_lists')) {
            Schema::create('selection_offline_lists', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('selection_event_id');
                $table->unsignedBigInteger('selection_protocol_id');
                $table->json('shuffled_donor_ids');
                $table->unsignedInteger('cursor')->default(0);
                $table->boolean('is_single_use')->default(true);
                $table->timestamp('printed_at')->nullable();
                $table->timestamps();

                $table->foreign('selection_event_id', 'sol_event_fk')
                    ->references('id')->on('selection_events')->cascadeOnDelete();
                $table->foreign('selection_protocol_id', 'sol_protocol_fk')
                    ->references('id')->on('selection_protocols')->cascadeOnDelete();
            });
        }

        if (!Schema::hasTable('selection_offline_list_consumptions')) {
            Schema::create('selection_offline_list_consumptions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('selection_offline_list_id');
                $table->unsignedInteger('list_index');
                $table->string('donor_id');
                $table->unsignedBigInteger('employee_id')->nullable();
                $table->unsignedBigInteger('selected_employee_id')->nullable();
                $table->unsignedBigInteger('replaces_selected_employee_id')->nullable();
                $table->timestamp('consumed_at');
                $table->timestamps();

                $table->unique(['selection_offline_list_id', 'list_index'], 'solc_list_index_uq');
                $table->foreign('selection_offline_list_id', 'solc_list_fk')
                    ->references('id')->on('selection_offline_lists')->cascadeOnDelete();
                $table->foreign('employee_id', 'solc_employee_fk')
                    ->references('id')->on('employees')->nullOnDelete();
                $table->foreign('selected_employee_id', 'solc_selected_fk')
                    ->references('id')->on('selected_employees')->nullOnDelete();
                $table->foreign('replaces_selected_employee_id', 'solc_replaces_fk')
                    ->references('id')->on('selected_employees')->nullOnDelete();
            });
        } else {
            // Repair a partial create that left the table without short-named FKs.
            Schema::table('selection_offline_list_consumptions', function (Blueprint $table) {
                try {
                    $table->unique(['selection_offline_list_id', 'list_index'], 'solc_list_index_uq');
                } catch (\Throwable $e) {
                    // already exists
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('selection_offline_list_consumptions');
        Schema::dropIfExists('selection_offline_lists');

        if (Schema::hasColumn('selected_employees', 'donor_id')) {
            Schema::table('selected_employees', function (Blueprint $table) {
                $table->dropColumn([
                    'donor_id',
                    'draw_pool',
                    'pool_range_max',
                    'print_order',
                    'replacement_reason',
                ]);
            });
        }

        if (Schema::hasColumn('selection_protocols', 'alternate_mode')) {
            Schema::table('selection_protocols', function (Blueprint $table) {
                $table->dropColumn(['alternate_mode', 'randomize_alternate_print_order']);
            });
        }
    }
};
