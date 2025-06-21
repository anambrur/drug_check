<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // php artisan migrate:rollback --path=/database/migrations/2025_05_02_093512_create_random_selections_table.php

        Schema::create('selection_protocols', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->foreignId('client_id')->constrained('client_profiles')->onDelete('cascade');
            $table->foreignId('test_id')->constrained('test_admins')->onDelete('cascade');
            $table->enum('group', ['DOT', 'NON_DOT', 'DOT_AGENCY', 'ALL']);
            $table->foreignId('dot_agency_id')->constrained('dot_agencies')->onDelete('cascade')->nullable();
            $table->string('department_filter')->nullable();
            $table->string('shift_filter')->nullable();
            $table->boolean('exclude_previously_selected')->default(false);
            $table->enum('selection_requirement_type', ['NUMBER', 'PERCENTAGE']);
            $table->integer('selection_requirement_value');
            $table->enum('selection_period', ['YEARLY', 'QUARTERLY', 'MONTHLY', 'MANUAL']);
            $table->integer('monthly_selection_day')->nullable();
            $table->json('manual_dates')->nullable();
            $table->enum('alternates_type', ['NUMBER', 'PERCENTAGE'])->nullable();
            $table->integer('alternates_value')->default(0);
            $table->boolean('automatic')->default(true);
            $table->boolean('calculate_pool_average')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('protocol_extra_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('selection_protocol_id')->constrained('selection_protocols')->onDelete('cascade');
            $table->foreignId('test_id')->constrained('test_admins')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('protocol_sub_selections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('selection_protocol_id')->constrained('selection_protocols')->onDelete('cascade');
            $table->foreignId('test_id')->constrained('test_admins')->onDelete('cascade');
            $table->enum('requirement_type', ['NUMBER', 'PERCENTAGE']);
            $table->integer('requirement_value');
            $table->timestamps();
        });

        Schema::create('selection_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('selection_protocol_id')->constrained('selection_protocols')->onDelete('cascade');
            $table->dateTime('selection_date');
            $table->integer('pool_size');
            $table->json('selection_pool');
            $table->enum('status', ['PENDING', 'COMPLETED', 'CANCELLED']);
            $table->timestamps();
        });

        Schema::create('selected_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('selection_event_id')->constrained('selection_events')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('test_id')->constrained('test_admins')->onDelete('cascade');
            $table->enum('selection_type', ['PRIMARY', 'SUB', 'ALTERNATE', 'EXTRA']);
            $table->integer('random_number');
            $table->boolean('is_excused')->default(false);
            $table->boolean('is_refused')->default(false);
            $table->foreignId('alternate_replaces_id')->nullable()->constrained('selected_employees')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('selected_employees');
        Schema::dropIfExists('selection_events');
        Schema::dropIfExists('protocol_sub_selections');
        Schema::dropIfExists('protocol_extra_tests');
        Schema::dropIfExists('selection_protocols');
    }
};
