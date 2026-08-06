<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_order_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type', 32); // dot | non_dot | consortium
            $table->string('title');
            $table->string('body')->nullable();
            $table->morphs('notifiable');
            $table->string('link_route');
            $table->json('link_params')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->unique(['notifiable_type', 'notifiable_id', 'type'], 'admin_order_notifications_unique');
            $table->index('read_at');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_order_notifications');
    }
};
