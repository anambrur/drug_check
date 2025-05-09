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
        Schema::create('m_r_o_s', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('doctor_name')->nullable();
            $table->string('mro_address')->nullable();
            $table->text('signature')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_r_o_s');
    }
};