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
        Schema::table('proyek_timesheet_uploads', function (Blueprint $table) {
            $table->date('work_date')->nullable()->after('version_no');
            $table->unsignedInteger('duration_days')->nullable()->after('work_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proyek_timesheet_uploads', function (Blueprint $table) {
            $table->dropColumn(['work_date', 'duration_days']);
        });
    }
};
