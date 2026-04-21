<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('emergency_contact_name')->nullable()->after('phone');
            $table->string('emergency_contact_phone', 30)->nullable()->after('emergency_contact_name');
            $table->string('bpjs_ketenagakerjaan_number', 100)->nullable()->after('identity_number');
            $table->string('bpjs_kesehatan_number', 100)->nullable()->after('bpjs_ketenagakerjaan_number');
            $table->text('important_information')->nullable()->after('religion');
            $table->string('last_education')->nullable()->after('important_information');
            $table->string('last_education_file_path')->nullable()->after('last_education');
            $table->string('last_education_file_name')->nullable()->after('last_education_file_path');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'emergency_contact_name',
                'emergency_contact_phone',
                'bpjs_ketenagakerjaan_number',
                'bpjs_kesehatan_number',
                'important_information',
                'last_education',
                'last_education_file_path',
                'last_education_file_name',
            ]);
        });
    }
};
