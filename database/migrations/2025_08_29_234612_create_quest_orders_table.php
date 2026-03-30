<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quest_orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('payment_intent_id')->nullable();

            // Quest Identifiers
            $table->string('quest_order_id')->nullable()->unique();
            $table->string('reference_test_id')->nullable()->unique();
            $table->string('client_reference_id');

            // -------------------------------------------------------------------------
            // ORDER STATUS — written by the inbound ResponseURL webhook (Section 3.1).
            // Quest pushes these as the specimen moves through its lifecycle.
            // order_status values: COLLECTED | ATLAB | PENDINGMRO | SUSPENDED |
            //                      SITESELECTED | PENDINGFAX | PARTIAL
            // order_status_screen_type values: drug | alcohol | physical
            // -------------------------------------------------------------------------
            $table->string('order_status')->nullable()->index();
            $table->string('order_status_screen_type')->nullable();   // drug | alcohol | physical
            $table->timestamp('order_status_datetime')->nullable();   // UTC timestamp from Quest
            $table->timestamp('order_status_updated_at')->nullable(); // when WE wrote this

            // -------------------------------------------------------------------------
            // ORDER RESULT — written by the inbound ResponseURL webhook (Section 3.2).
            // This is the FINAL adjudicated result from the MRO.
            // order_result values: Negative | Positive | Cancelled | No Show Expired |
            //                      Positive Dilute | Donor Refused | COMPLETE | etc.
            // -------------------------------------------------------------------------
            $table->string('order_result')->nullable()->index();
            $table->string('order_result_screen_type')->nullable();    // drug | alcohol | physical
            $table->timestamp('order_result_datetime')->nullable();    // UTC timestamp from Quest
            $table->timestamp('order_result_updated_at')->nullable();  // when WE wrote this

            // -------------------------------------------------------------------------
            // SPECIMEN DATA — arrives with status/result webhooks
            // -------------------------------------------------------------------------
            $table->string('specimen_id')->nullable();
            $table->string('lab_accession_number')->nullable();
            $table->timestamp('collected_datetime')->nullable();

            // -------------------------------------------------------------------------
            // PHYSICAL SUB-BLOCK (Section 4.32)
            // Only populated for physical exam orders. Stored as JSON because the
            // Physical node is deeply nested and sparse.
            // -------------------------------------------------------------------------
            $table->json('physical_data')->nullable();

            // -------------------------------------------------------------------------
            // RAW WEBHOOK PAYLOADS — kept for audit and debugging
            // -------------------------------------------------------------------------
            $table->text('status_raw_xml')->nullable();  // last inbound status SOAP body
            $table->text('result_raw_xml')->nullable();  // inbound result SOAP body

            // Donor Information
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->string('primary_id');
            $table->string('primary_id_type')->nullable();
            $table->date('dob')->nullable();
            $table->string('primary_phone');
            $table->string('secondary_phone')->nullable();
            $table->string('email')->nullable();
            $table->string('zip_code')->nullable();

            // Test Information
            $table->unsignedBigInteger('portfolio_id')->nullable();
            $table->text('portfolio_name')->nullable();
            $table->text('unit_codes')->nullable();
            $table->string('dot_test', 1);
            $table->string('testing_authority')->nullable();
            $table->string('reason_for_test_id')->nullable();
            $table->string('physical_reason_for_test_id')->nullable();
            $table->string('collection_site_id')->nullable();
            $table->string('observed_requested', 1)->default('N');
            $table->string('split_specimen_requested', 1)->default('N');
            $table->text('order_comments')->nullable();

            // Client Info
            $table->string('lab_account');
            $table->string('csl')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('telephone_number')->nullable();

            // Timing
            $table->timestamp('end_datetime')->nullable();
            $table->integer('end_datetime_timezone_id')->nullable();
            $table->timestamp('expired_at')->nullable();

            // API Interaction Logging
            $table->string('response_url')->nullable();
            $table->text('request_xml')->nullable();
            $table->text('create_response_xml')->nullable();
            $table->string('create_response_status');
            $table->text('create_error')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'created_at']);
            $table->index('client_reference_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quest_orders');
    }
};
