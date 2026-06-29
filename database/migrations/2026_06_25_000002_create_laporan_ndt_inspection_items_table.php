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
        Schema::create('laporan_ndt_inspection_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_ndt_report_id')
                ->constrained('laporan_ndt_reports')
                ->cascadeOnDelete();

            $table->unsignedInteger('sort_order')->default(1);
            $table->unsignedBigInteger('description_master_id')->nullable();
            $table->string('description')->nullable();
            $table->string('code')->nullable();
            $table->string('id_no')->nullable();
            $table->decimal('diameter_mm', 10, 2)->nullable();
            $table->decimal('length_mm', 10, 2)->nullable();
            $table->decimal('thickness_mm', 10, 2)->nullable();
            $table->enum('result', ['acc', 'reject'])->nullable();
            $table->text('remark')->nullable();
            $table->json('sketch_annotations')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_ndt_inspection_items');
    }
};
