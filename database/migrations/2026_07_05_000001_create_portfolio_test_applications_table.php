<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_test_applications', function (Blueprint $table) {
            $table->id();

            $table->enum('test_type', ['dot', 'non_dot']);
            $table->foreignId('portfolio_id')->constrained('portfolios')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();

            // Non-DOT applicant fields
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('date')->nullable();
            $table->string('gender')->nullable();
            $table->string('preferred_location')->nullable();
            $table->string('employee_name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('accounting_email')->nullable();
            $table->string('reason_for_testing')->nullable();
            $table->string('country', 2)->nullable();

            $table->unsignedBigInteger('amount');
            $table->string('stripe_checkout_session_id')->nullable()->index();
            $table->string('stripe_payment_intent_id')->nullable()->index();
            $table->string('payment_status')->default('pending');
            $table->string('status')->default('Pending Payment');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_test_applications');
    }
};
