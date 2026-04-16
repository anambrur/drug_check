<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stripe_webhook_events', function (Blueprint $table) {
            // Indexed plain column so we can quickly find all events for a given PaymentIntent
            $table->string('payment_intent_id')->nullable()->index()->after('stripe_event_id');
        });
    }

    public function down(): void
    {
        Schema::table('stripe_webhook_events', function (Blueprint $table) {
            $table->dropIndex(['payment_intent_id']);
            $table->dropColumn('payment_intent_id');
        });
    }
};
