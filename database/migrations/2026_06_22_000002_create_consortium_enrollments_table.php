<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('consortium_enrollments', function (Blueprint $table) {
            $table->id();
            
            // Company Details
            $table->string('company_name');
            $table->string('dba_name')->nullable();
            $table->string('dot_number');
            $table->string('mc_number')->nullable();
            $table->string('ein_number')->nullable();
            
            // DER Contact Details
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            
            // Address Details
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('zip_code');
            
            // Enrollment Details
            $table->string('selected_plan');
            $table->integer('driver_count');
            $table->text('notes')->nullable();
            
            // Financials and Stripe (stored in cents)
            $table->unsignedBigInteger('amount');
            $table->string('stripe_checkout_session_id')->nullable()->index();
            $table->string('stripe_payment_intent_id')->nullable()->index();
            
            // Statuses
            $table->string('status')->default('Pending Payment');
            $table->string('payment_status')->default('pending');
            $table->text('internal_notes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consortium_enrollments');
    }
};
