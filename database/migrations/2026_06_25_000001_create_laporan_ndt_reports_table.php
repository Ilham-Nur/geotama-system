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
        Schema::create('laporan_ndt_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_pekerjaan_id')
                ->unique()
                ->constrained('laporan_pekerjaan')
                ->cascadeOnDelete();

            $table->string('report_no')->unique();
            $table->string('service_code', 10);
            $table->unsignedInteger('service_sequence');

            $table->string('part_no')->nullable();
            $table->text('description')->nullable();
            $table->string('material')->nullable();
            $table->string('temperature')->nullable();

            $table->unsignedBigInteger('procedure_id')->nullable();
            $table->unsignedBigInteger('criteria_id')->nullable();
            $table->unsignedBigInteger('testing_standard_id')->nullable();

            $table->json('surface_conditions')->nullable();
            $table->json('preparation_conditions')->nullable();
            $table->json('stage_ofs')->nullable();
            $table->json('examinations')->nullable();
            $table->json('techniques')->nullable();
            $table->json('test_types')->nullable();
            $table->json('penetrant_applications')->nullable();
            $table->json('material_rows')->nullable();
            $table->json('dwell_times')->nullable();
            $table->string('blacklight_intensity')->nullable();

            $table->string('examiner_name')->nullable();
            $table->string('examiner_position')->nullable();
            $table->string('qc_inspector_name')->nullable();
            $table->string('qc_inspector_position')->nullable();
            $table->string('owner_representative_name')->nullable();
            $table->string('owner_representative_position')->nullable();
            $table->string('surveyor_name')->nullable();
            $table->string('surveyor_position')->nullable();

            $table->enum('status', ['draft', 'submit'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_ndt_reports');
    }
};
