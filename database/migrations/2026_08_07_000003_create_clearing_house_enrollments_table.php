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
        Schema::create('clearing_house_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('client_profile_id')->nullable()->constrained('client_profiles')->nullOnDelete();

            // Company Details
            $table->string('company_name');
            $table->string('dba_name')->nullable();
            $table->string('dot_number');
            $table->string('mc_number')->nullable();
            $table->string('ein_number');
            $table->string('company_phone');

            // DER / Authorized Person
            $table->string('first_name');
            $table->string('last_name');
            $table->string('job_title');
            $table->string('email');
            $table->string('phone');

            // Address Details
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('zip_code');

            // Clearinghouse-specific onboarding
            $table->boolean('is_owner_operator')->default(false);
            $table->string('clearinghouse_registered', 20); // yes|no|unsure
            $table->boolean('authorize_conduct_queries')->default(true);
            $table->boolean('authorize_report_violations')->default(true);
            $table->boolean('authorize_report_rtd')->default(true);
            $table->boolean('acknowledge_designate_ctpa')->default(false);
            $table->boolean('acknowledge_query_plan')->default(false);

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
            $table->timestamp('notifications_sent_at')->nullable();
            $table->timestamp('company_notified_at')->nullable();
            $table->timestamp('admin_notified_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clearing_house_enrollments');
    }
};
