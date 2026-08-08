<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('result_recordings', function (Blueprint $table) {
            $table->foreignId('selection_event_id')
                ->nullable()
                ->after('employee_id')
                ->constrained('selection_events')
                ->nullOnDelete();

            $table->foreignId('selected_employee_id')
                ->nullable()
                ->after('selection_event_id')
                ->constrained('selected_employees')
                ->nullOnDelete();

            $table->index(['selection_event_id', 'selected_employee_id']);
        });
    }

    public function down(): void
    {
        Schema::table('result_recordings', function (Blueprint $table) {
            $table->dropForeign(['selection_event_id']);
            $table->dropForeign(['selected_employee_id']);
            $table->dropIndex(['selection_event_id', 'selected_employee_id']);
            $table->dropColumn(['selection_event_id', 'selected_employee_id']);
        });
    }
};
