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
        Schema::create('result_recordings', function (Blueprint $table) {
            $table->id();
            // Company Information
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('client_profiles')->onDelete('cascade');

            // Test Information
            $table->string('reason_for_test');
            $table->unsignedBigInteger('test_admin_id');
            $table->foreign('test_admin_id')->references('id')->on('test_admins')->onDelete('cascade');

            // Service Providers
            $table->unsignedBigInteger('laboratory_id')->nullable();
            $table->foreign('laboratory_id')->references('id')->on('laboratories')->onDelete('cascade');
            $table->unsignedBigInteger('mro_id')->nullable();
            $table->foreign('mro_id')->references('id')->on('m_r_o_s')->onDelete('cascade');

            // Collection Details
            $table->string('collection_location')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->dateTime('collection_datetime');
            $table->date('date_of_collection');
            $table->time('time_of_collection');

            // Additional Information
            $table->text('note')->nullable();
            $table->enum('status', ['positive', 'negative', 'refused','excused','cancelled','pending','saved','collection only'])->default('pending');

            // Timestamps
            $table->timestamps();
            $table->softDeletes();
        });


        // Create a separate table for panel test results
        Schema::create('result_panels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('result_id')->nullable();
            $table->foreign('result_id')->references('id')->on('result_recordings')->onDelete('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('panel_id')->nullable();
            $table->foreign('panel_id')->references('id')->on('panels')->onDelete('cascade');

            $table->string('drug_name')->nullable();
            $table->string('drug_code')->nullable();
            $table->string('result')->nullable(); // 'positive' or 'negative'
            $table->string('cut_off_level')->nullable();
            $table->string('conf_level')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('result_panels');
        Schema::dropIfExists('result_recordings');
    }
};
