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
        Schema::create('proyek_timesheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyek_id')->constrained('proyek')->cascadeOnDelete();
            $table->string('form_no')->unique();
            $table->date('inspection_date')->nullable();
            $table->enum('status', ['generated', 'in_field', 'uploaded_partial', 'completed', 'verified'])->default('generated');
            $table->text('remarks')->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyek_timesheets');
    }
};
