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
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->text('message');
            $table->string('address')->nullable();
            $table->string('preferred_location')->nullable();
            $table->string('employee_name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('accounting_email')->nullable();
            $table->string('test_category')->nullable();
            $table->date('date')->nullable();
            $table->string('gender')->nullable();
            $table->json('services')->nullable();
            $table->integer('read')->default(0);
            $table->string('company_city')->nullable();
            $table->string('company_state')->nullable();
            $table->string('company_zip')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('certificate_path')->nullable();
            $table->date('certificate_start_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};
