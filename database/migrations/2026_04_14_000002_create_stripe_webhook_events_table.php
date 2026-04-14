<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stripe_webhook_events', function (Blueprint $table) {
            $table->id();

            $table->string('stripe_event_id')->unique();
            $table->string('type')->index();
            $table->string('api_version')->nullable();
            $table->boolean('livemode')->default(false)->index();
            $table->unsignedInteger('stripe_created')->nullable()->index();

            $table->json('payload');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stripe_webhook_events');
    }
};

