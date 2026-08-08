<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quest_webhook_dead_letters', function (Blueprint $table) {
            $table->id();
            $table->string('payload_type', 32);
            $table->string('quest_order_id')->nullable()->index();
            $table->string('reference_test_id')->nullable()->index();
            $table->string('client_reference_id')->nullable();
            $table->string('screen_type', 16)->nullable();
            $table->string('status_or_result_id')->nullable();
            $table->longText('raw_body');
            $table->string('client_ip', 45)->nullable();
            $table->string('reason', 64);
            $table->timestamp('replayed_at')->nullable();
            $table->timestamps();

            $table->index(['reason', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quest_webhook_dead_letters');
    }
};
