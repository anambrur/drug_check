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
        Schema::create('protocol_client', function (Blueprint $table) {
            $table->id();
            $table->foreignId('selection_protocol_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_profile_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['selection_protocol_id', 'client_profile_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('protocol_client');
    }
};
