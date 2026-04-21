<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->enum('certificate_type', ['internal', 'external']);
            $table->string('certificate_name');
            $table->date('issued_at')->nullable();
            $table->date('expired_at')->nullable();
            $table->string('issuer')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_certificates');
    }
};
