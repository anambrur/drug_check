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
        if (!Schema::hasTable('random_consortia')) {
            Schema::create('random_consortia', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->unsignedBigInteger('language_id');
                $table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
                $table->enum('style', ['style1'])->default('style1');
                $table->text('description')->nullable();
                $table->text('short_description')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('random_consortia');
    }
};
