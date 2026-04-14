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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('no_aset')->unique();
            $table->string('nama');
            $table->string('merek')->nullable();
            $table->string('no_seri')->nullable();
            $table->string('lokasi');
            $table->unsignedInteger('jumlah');
            $table->decimal('harga', 15, 2);
            $table->decimal('total', 15, 2);
            $table->string('file_faktur')->nullable();
            $table->unsignedSmallInteger('tahun');
            $table->enum('remark', ['baik', 'perlu perbaikan', 'rusak', 'hilang'])->nullable();
            $table->string('gambar')->nullable();
            $table->uuid('qr_token')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
