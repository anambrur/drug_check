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
        Schema::create('panel_test_admin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_admin_id')->constrained('test_admins')->onDelete('cascade');
            $table->foreignId('panel_id')->constrained('panels')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('panel_test_admin');
    }
};
