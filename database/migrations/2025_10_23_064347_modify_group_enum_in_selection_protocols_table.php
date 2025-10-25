<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Method 1: Using raw SQL (recommended for enum modifications)
        DB::statement("ALTER TABLE selection_protocols MODIFY COLUMN `group` ENUM('DOT', 'NON_DOT', 'DOT_AGENCY', 'ALL', 'FMCSA', 'FRA', 'FTA', 'FAA', 'PHMSA', 'RSPA', 'USCG') NOT NULL");

        // Alternative Method 2: If you prefer Schema builder (may not work on all database systems)
        // Schema::table('selection_protocols', function (Blueprint $table) {
        //     $table->enum('group', ['DOT', 'NON_DOT', 'DOT_AGENCY', 'ALL', 'FMCSA', 'FRA', 'FTA', 'FAA', 'PHMSA', 'RSPA', 'USCG'])
        //           ->change();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE selection_protocols MODIFY COLUMN `group` ENUM('DOT', 'NON_DOT', 'DOT_AGENCY', 'ALL') NOT NULL");

        // Alternative Method 2 reversal:
        // Schema::table('selection_protocols', function (Blueprint $table) {
        //     $table->enum('group', ['DOT', 'NON_DOT', 'DOT_AGENCY', 'ALL'])
        //           ->change();
        // });
    }
};