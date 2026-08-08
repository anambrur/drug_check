<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Make Clearing House "Owner Operator" accept any driver count (not fixed to 1).
     */
    public function up(): void
    {
        DB::table('clearing_house_plans')
            ->where('slug', 'owner-operator')
            ->update([
                'min_drivers' => 1,
                'max_drivers' => null,
                'description' => 'Flexible plan for owner-operators — enter any number of drivers.',
                'updated_at' => now(),
            ]);
    }

    /**
     * Revert Owner Operator to a fixed 1-driver plan.
     */
    public function down(): void
    {
        DB::table('clearing_house_plans')
            ->where('slug', 'owner-operator')
            ->update([
                'min_drivers' => 1,
                'max_drivers' => 1,
                'description' => 'Perfect for individual owner-operators with a single driver.',
                'updated_at' => now(),
            ]);
    }
};
