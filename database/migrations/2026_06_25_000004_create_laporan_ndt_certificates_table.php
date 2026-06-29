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
        Schema::create('laporan_ndt_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_ndt_report_id')
                ->constrained('laporan_ndt_reports')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('certificate_id')->nullable();
            $table->string('certificate_title')->nullable();
            $table->string('certificate_no')->nullable();
            $table->string('file_path')->nullable();
            $table->unsignedInteger('sort_order')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_ndt_certificates');
    }
};
