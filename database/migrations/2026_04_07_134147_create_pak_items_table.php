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
        Schema::create('pak_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pak_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories');

            $table->string('name');
            $table->text('description')->nullable();

            $table->decimal('qty', 10, 2)->default(0);
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pak_items');
    }
};
