<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_tugas', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat')->unique();
            $table->foreignId('proyek_id')->constrained('proyek')->cascadeOnDelete();
            $table->date('tanggal_berangkat');
            $table->date('tanggal_kembali');
            $table->string('transportasi')->nullable();
            $table->text('keterangan')->nullable();
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_tugas');
    }
};
