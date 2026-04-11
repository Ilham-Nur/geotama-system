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
        Schema::create('permohonans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor')->unique();

            $table->string('nama_perusahaan');
            $table->text('alamat');
            $table->string('nama_pic');
            $table->string('no_telp');
            $table->string('email')->nullable();

            $table->enum('testuji', ['quality_internal', 'quality_external']);
            $table->string('testuji_external_keterangan')->nullable();

            $table->text('lokasi');
            $table->string('nama_proyek');
            $table->text('permintaan_khusus')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonans');
    }
};
