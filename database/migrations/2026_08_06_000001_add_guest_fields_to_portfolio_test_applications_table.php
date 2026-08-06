<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portfolio_test_applications', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('portfolio_test_applications', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->boolean('is_guest')->default(false)->after('user_id');
            $table->string('guest_access_token', 64)->nullable()->unique()->after('is_guest');
        });

        Schema::table('portfolio_test_applications', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('portfolio_test_applications', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['is_guest', 'guest_access_token']);
        });

        Schema::table('portfolio_test_applications', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
