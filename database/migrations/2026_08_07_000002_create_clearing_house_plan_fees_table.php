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
        Schema::create('clearing_house_plan_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clearing_house_plan_id')->constrained('clearing_house_plans')->onDelete('cascade');
            $table->string('fee_key', 100);
            $table->string('fee_label', 255);
            $table->unsignedInteger('fee_amount'); // stored in cents
            $table->enum('fee_type', ['flat', 'per_driver'])->default('flat');
            $table->unsignedTinyInteger('display_order')->default(0);
            $table->timestamps();

            $table->index('clearing_house_plan_id');
            $table->index(['clearing_house_plan_id', 'fee_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clearing_house_plan_fees');
    }
};
