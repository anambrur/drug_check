<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Local context
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('portfolio_id')->nullable()->index();

            // Stripe identifiers
            $table->string('stripe_payment_intent_id')->unique();
            $table->string('stripe_charge_id')->nullable()->index();

            // Money
            $table->unsignedBigInteger('amount'); // cents
            $table->string('currency', 3)->default('usd');
            $table->string('status')->index(); // succeeded/processing/requires_payment_method/canceled/failed/etc.
            $table->string('app_tag')->nullable()->index();
            $table->string('app_env')->nullable()->index();

            // Customer + metadata (safe fields only)
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable()->index();
            $table->string('customer_phone')->nullable();
            $table->string('country', 2)->nullable();
            $table->string('test_name')->nullable();
            $table->string('description')->nullable();

            // Outcome
            $table->timestamp('paid_at')->nullable()->index();
            $table->unsignedBigInteger('refunded_amount')->nullable(); // cents
            $table->timestamp('refunded_at')->nullable();
            $table->text('failure_message')->nullable();

            // Raw Stripe objects for debugging/auditing (optional)
            $table->json('stripe_payment_intent')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

