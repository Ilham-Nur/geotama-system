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
        Schema::create('laporan_foto_lampiran', function (Blueprint $table) {
            $table->id();

            $table->foreignId('laporan_pekerjaan_id')
                ->constrained('laporan_pekerjaan')
                ->cascadeOnDelete();

            $table->string('nama_file');       // nama asli foto dari user
            $table->string('path');            // path di storage, misal: laporan/lampiran/namafile.jpg
            $table->string('mime_type', 100);  // image/jpeg | image/png
            $table->unsignedBigInteger('size')->default(0); // ukuran file dalam bytes

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_foto_lampiran');
    }
};
