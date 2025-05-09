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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_profile_id')->constrained('client_profiles')->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->string('department')->nullable();
            $table->string('shift')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('employee_id')->unique();
            $table->date('background_check_date')->nullable();
            $table->string('ssn')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('short_description')->nullable();
            $table->string('cdl_state')->nullable();
            $table->string('cdl_number')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('dot')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};