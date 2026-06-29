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
        Schema::create('laporan_ndt_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_ndt_report_id')
                ->constrained('laporan_ndt_reports')
                ->cascadeOnDelete();
            $table->foreignId('inspection_item_id')
                ->nullable()
                ->constrained('laporan_ndt_inspection_items')
                ->cascadeOnDelete();

            $table->enum('type', ['before', 'during', 'after']);
            $table->string('file_path');
            $table->string('original_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->unsignedInteger('sort_order')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_ndt_photos');
    }
};
