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
        Schema::create('proyek_timesheet_uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyek_timesheet_id')->constrained('proyek_timesheets')->cascadeOnDelete();
            $table->foreignId('proyek_id')->constrained('proyek')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->unsignedInteger('version_no')->default(1);
            $table->string('notes')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyek_timesheet_uploads');
    }
};
