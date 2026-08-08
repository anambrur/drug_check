<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portfolio_test_applications', function (Blueprint $table) {
            $table->timestamp('customer_notified_at')->nullable()->after('quest_order_id');
            $table->timestamp('admin_notified_at')->nullable()->after('customer_notified_at');
            $table->timestamp('notifications_sent_at')->nullable()->after('admin_notified_at');
        });
    }

    public function down(): void
    {
        Schema::table('portfolio_test_applications', function (Blueprint $table) {
            $table->dropColumn([
                'customer_notified_at',
                'admin_notified_at',
                'notifications_sent_at',
            ]);
        });
    }
};
