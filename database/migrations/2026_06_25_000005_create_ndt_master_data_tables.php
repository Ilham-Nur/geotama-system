<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ndt_procedures', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ndt_acceptance_criteria', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ndt_testing_standards', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ndt_inspection_descriptions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->string('sketch_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ndt_certificates', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('certificate_no')->nullable();
            $table->string('type')->nullable();
            $table->date('issued_at')->nullable();
            $table->date('expired_at')->nullable();
            $table->string('file_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ndt_approval_people', function (Blueprint $table) {
            $table->id();
            $table->enum('role', ['examiner', 'qc_inspector', 'owner_representative', 'surveyor']);
            $table->string('name');
            $table->string('position')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('laporan_ndt_reports', function (Blueprint $table) {
            $table->foreign('procedure_id')->references('id')->on('ndt_procedures')->nullOnDelete();
            $table->foreign('criteria_id')->references('id')->on('ndt_acceptance_criteria')->nullOnDelete();
            $table->foreign('testing_standard_id')->references('id')->on('ndt_testing_standards')->nullOnDelete();
        });

        Schema::table('laporan_ndt_inspection_items', function (Blueprint $table) {
            $table->foreign('description_master_id')->references('id')->on('ndt_inspection_descriptions')->nullOnDelete();
        });

        Schema::table('laporan_ndt_certificates', function (Blueprint $table) {
            $table->foreign('certificate_id')->references('id')->on('ndt_certificates')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('laporan_ndt_certificates', function (Blueprint $table) {
            $table->dropForeign(['certificate_id']);
        });

        Schema::table('laporan_ndt_inspection_items', function (Blueprint $table) {
            $table->dropForeign(['description_master_id']);
        });

        Schema::table('laporan_ndt_reports', function (Blueprint $table) {
            $table->dropForeign(['procedure_id']);
            $table->dropForeign(['criteria_id']);
            $table->dropForeign(['testing_standard_id']);
        });

        Schema::dropIfExists('ndt_approval_people');
        Schema::dropIfExists('ndt_certificates');
        Schema::dropIfExists('ndt_inspection_descriptions');
        Schema::dropIfExists('ndt_testing_standards');
        Schema::dropIfExists('ndt_acceptance_criteria');
        Schema::dropIfExists('ndt_procedures');
    }
};
