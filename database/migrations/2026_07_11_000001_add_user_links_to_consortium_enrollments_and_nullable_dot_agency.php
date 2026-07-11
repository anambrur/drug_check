<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Production schema has an index named like a FK but no actual foreign key constraint.
        DB::statement('ALTER TABLE client_profiles MODIFY dot_agency_id BIGINT UNSIGNED NULL');

        Schema::table('consortium_enrollments', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            $table->foreignId('client_profile_id')->nullable()->after('user_id')->constrained('client_profiles')->nullOnDelete();
            $table->timestamp('notifications_sent_at')->nullable()->after('internal_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consortium_enrollments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropConstrainedForeignId('client_profile_id');
            $table->dropColumn('notifications_sent_at');
        });

        $fallbackAgencyId = DB::table('dot_agencies')->orderBy('id')->value('id');
        if ($fallbackAgencyId) {
            DB::table('client_profiles')->whereNull('dot_agency_id')->update([
                'dot_agency_id' => $fallbackAgencyId,
            ]);
        }

        DB::statement('ALTER TABLE client_profiles MODIFY dot_agency_id BIGINT UNSIGNED NOT NULL');
    }
};
