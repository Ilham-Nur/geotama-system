<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('contract_type');
            $table->string('contract_number')->nullable();
            $table->date('signing_date');
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->date('effective_date')->nullable();
            $table->decimal('salary', 15, 2)->nullable();
            $table->string('generated_file_path');
            $table->string('generated_file_name');
            $table->unsignedBigInteger('generated_file_size')->nullable();
            $table->string('hardcopy_file_path')->nullable();
            $table->string('hardcopy_file_name')->nullable();
            $table->unsignedBigInteger('hardcopy_file_size')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_contracts');
    }
};
