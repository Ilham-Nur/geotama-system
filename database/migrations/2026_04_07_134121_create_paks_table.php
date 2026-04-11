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
        Schema::create('paks', function (Blueprint $table) {
            $table->id();

            $table->string('pak_number')->unique();
            $table->string('pak_name');

            // 🔥 JSON permohonan + dokumen
            $table->json('permohonan_data');

            // 🔥 Financial
            $table->decimal('project_value', 15, 2);
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->decimal('profit', 15, 2)->default(0);
            $table->decimal('profit_percentage', 5, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paks');
    }
};
