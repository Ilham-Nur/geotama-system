<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('laporan_ndt_inspection_items') || !Schema::hasColumn('laporan_ndt_inspection_items', 'result')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE laporan_ndt_inspection_items MODIFY result VARCHAR(20) NULL");
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('laporan_ndt_inspection_items') || !Schema::hasColumn('laporan_ndt_inspection_items', 'result')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE laporan_ndt_inspection_items MODIFY result ENUM('acc', 'reject') NULL");
        }
    }
};
