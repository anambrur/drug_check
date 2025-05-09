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
        Schema::create('test_admins', function (Blueprint $table) {
            $table->id();
            $table->string('test_name');
            $table->string('specimen')->nullable();
            $table->string('method')->nullable();
            $table->string('regulation')->nullable();
            $table->string('description')->nullable();
            $table->foreignId('laboratory_id')
                ->nullable()
                ->constrained('laboratories')
                ->onDelete('cascade');

            $table->foreignId('mro_id')
                ->nullable()
                ->constrained('m_r_o_s')
                ->onDelete('cascade');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_admins');
    }
};