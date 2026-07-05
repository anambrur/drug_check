<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portfolio_test_applications', function (Blueprint $table) {
            $table->string('middle_name')->nullable()->after('last_name');
            $table->string('primary_id')->nullable()->after('middle_name');
            $table->string('primary_id_type', 5)->nullable()->after('primary_id');
            $table->string('dob')->nullable()->after('primary_id_type');
            $table->string('secondary_phone')->nullable()->after('phone');
            $table->string('zip_code', 10)->nullable()->after('address');

            $table->string('dot_test', 1)->nullable()->after('country');
            $table->string('testing_authority', 10)->nullable()->after('dot_test');
            $table->unsignedTinyInteger('reason_for_test_id')->nullable()->after('testing_authority');
            $table->string('physical_reason_for_test_id', 2)->nullable()->after('reason_for_test_id');
            $table->string('collection_site_id', 6)->nullable()->after('physical_reason_for_test_id');
            $table->dateTime('end_datetime')->nullable()->after('collection_site_id');
            $table->unsignedTinyInteger('end_datetime_timezone_id')->nullable()->after('end_datetime');
            $table->string('observed_requested', 1)->default('N')->after('end_datetime_timezone_id');
            $table->string('split_specimen_requested', 1)->default('N')->after('observed_requested');
            $table->string('csl', 20)->nullable()->after('split_specimen_requested');
            $table->string('contact_name', 45)->nullable()->after('csl');
            $table->string('telephone_number', 20)->nullable()->after('contact_name');
            $table->string('order_comments', 250)->nullable()->after('telephone_number');

            $table->string('quest_submission_status')->default('pending')->after('status');
            $table->text('quest_submission_error')->nullable()->after('quest_submission_status');
            $table->string('quest_order_id')->nullable()->after('quest_submission_error');
        });
    }

    public function down(): void
    {
        Schema::table('portfolio_test_applications', function (Blueprint $table) {
            $table->dropColumn([
                'middle_name',
                'primary_id',
                'primary_id_type',
                'dob',
                'secondary_phone',
                'zip_code',
                'dot_test',
                'testing_authority',
                'reason_for_test_id',
                'physical_reason_for_test_id',
                'collection_site_id',
                'end_datetime',
                'end_datetime_timezone_id',
                'observed_requested',
                'split_specimen_requested',
                'csl',
                'contact_name',
                'telephone_number',
                'order_comments',
                'quest_submission_status',
                'quest_submission_error',
                'quest_order_id',
            ]);
        });
    }
};
