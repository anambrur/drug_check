<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('selected_employees', function (Blueprint $table) {
            $table->string('status', 32)->default('pending')->after('random_number');
            $table->boolean('notification_sent')->default(false)->after('status');
            $table->timestamp('notification_sent_at')->nullable()->after('notification_sent');
        });
    }

    public function down(): void
    {
        Schema::table('selected_employees', function (Blueprint $table) {
            $table->dropColumn(['status', 'notification_sent', 'notification_sent_at']);
        });
    }
};
