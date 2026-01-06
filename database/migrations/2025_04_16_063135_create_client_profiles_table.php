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
        Schema::create('client_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('company_name', 191);
            $table->text('short_description')->nullable();
            $table->string('address', 255);
            $table->string('city', 100);
            $table->string('state', 100);
            $table->string('zip', 20);
            $table->foreignId('dot_agency_id')->constrained('dot_agencies')->onDelete('cascade');
            $table->string('account_no', 20)->nullable();

            $table->string('phone', 20)->nullable();
            $table->string('fax', 20)->nullable();
            $table->string('shipping_address', 255)->nullable();

            $table->string('billing_contact_name', 100)->nullable();
            $table->string('billing_contact_email', 150)->nullable()->index();
            $table->string('billing_contact_phone', 20)->nullable();    

            $table->string('der_contact_name', 100);
            $table->string('der_contact_email', 150)->index();
            $table->string('der_contact_phone', 20)->nullable();

            $table->date('client_start_date')->nullable();
            $table->date('certificate_start_date')->nullable();
            $table->string('certificate_path')->nullable();
            $table->timestamp('certificate_generated_at')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_profiles');
    }
};
