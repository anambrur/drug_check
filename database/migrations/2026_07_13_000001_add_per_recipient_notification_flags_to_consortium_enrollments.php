<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consortium_enrollments', function (Blueprint $table) {
            $table->timestamp('company_notified_at')->nullable()->after('notifications_sent_at');
            $table->timestamp('admin_notified_at')->nullable()->after('company_notified_at');
        });
    }

    public function down(): void
    {
        Schema::table('consortium_enrollments', function (Blueprint $table) {
            $table->dropColumn(['company_notified_at', 'admin_notified_at']);
        });
    }
};
