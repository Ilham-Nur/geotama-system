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
        Schema::create('permohonan_item_layanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_item_id')->constrained('permohonan_items')->cascadeOnDelete();
            $table->foreignId('layanan_id')->constrained('layanans')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['permohonan_item_id', 'layanan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonan_item_layanan');
    }
};
