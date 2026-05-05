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
        Schema::create('laporan_pekerjaan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('proyek_id')
                ->constrained('proyek')
                ->cascadeOnDelete();

            // item mengacu ke tabel items (sesuaikan nama tabel jika berbeda)
            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('id')->on('permohonan_items')->cascadeOnDelete();

            // layanan mengacu ke tabel layanans (sesuaikan nama tabel jika berbeda)
            $table->unsignedBigInteger('layanan_id');
            $table->foreign('layanan_id')->references('id')->on('layanans')->cascadeOnDelete();

            $table->date('tanggal_pelaksanaan');

            // 'draft' = belum final, 'submit' = sudah dikirim
            $table->enum('action', ['draft', 'submit'])->default('draft');

            // user yang membuat laporan
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_pekerjaan');
    }
};
