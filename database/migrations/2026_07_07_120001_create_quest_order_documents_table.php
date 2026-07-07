<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quest_order_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quest_order_id')->constrained('quest_orders')->cascadeOnDelete();
            $table->string('screen_type', 20)->default('drug');
            $table->string('doc_type', 30);
            $table->string('file_path');
            $table->string('file_hash', 64)->nullable();
            $table->string('quest_specimen_id')->nullable();
            $table->timestamp('downloaded_at')->nullable();
            $table->foreignId('downloaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['quest_order_id', 'screen_type', 'doc_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quest_order_documents');
    }
};
