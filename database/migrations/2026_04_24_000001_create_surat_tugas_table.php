<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('surat_tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyek_id')
                ->constrained('proyek')
                ->cascadeOnDelete();
            $table->date('tanggal_berangkat');
            $table->date('tanggal_kembali');
            $table->string('transportasi');
            $table->text('keterangan')->nullable();
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_tugas');
    }
};
