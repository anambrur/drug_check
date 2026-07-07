<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quest_order_screens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quest_order_id')->constrained('quest_orders')->cascadeOnDelete();
            $table->string('screen_type', 20);
            $table->string('order_status')->nullable();
            $table->timestamp('order_status_datetime')->nullable();
            $table->string('order_result')->nullable();
            $table->timestamp('order_result_datetime')->nullable();
            $table->string('specimen_id')->nullable();
            $table->string('lab_accession_number')->nullable();
            $table->timestamp('collected_datetime')->nullable();
            $table->json('physical_data')->nullable();
            $table->text('status_raw_xml')->nullable();
            $table->text('result_raw_xml')->nullable();
            $table->timestamps();

            $table->unique(['quest_order_id', 'screen_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quest_order_screens');
    }
};
