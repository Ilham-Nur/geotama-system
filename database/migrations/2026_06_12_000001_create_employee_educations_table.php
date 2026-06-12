<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_educations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('education_level')->nullable();
            $table->string('institution_name');
            $table->string('major')->nullable();
            $table->unsignedSmallInteger('start_year')->nullable();
            $table->unsignedSmallInteger('end_year')->nullable();
            $table->boolean('is_current')->default(false);
            $table->string('grade')->nullable();
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->timestamps();
        });

        DB::table('employees')
            ->where(function ($query) {
                $query->whereNotNull('last_education')
                    ->orWhereNotNull('last_education_file_path');
            })
            ->orderBy('id')
            ->each(function ($employee) {
                DB::table('employee_educations')->insert([
                    'employee_id' => $employee->id,
                    'institution_name' => $employee->last_education ?: 'Pendidikan terakhir',
                    'file_path' => $employee->last_education_file_path,
                    'file_name' => $employee->last_education_file_name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_educations');
    }
};
