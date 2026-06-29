<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_ndt_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('laporan_ndt_reports', 'stage_ofs')) {
                $table->json('stage_ofs')->nullable()->after('preparation_conditions');
            }

            if (!Schema::hasColumn('laporan_ndt_reports', 'examinations')) {
                $table->json('examinations')->nullable()->after('stage_ofs');
            }

            if (!Schema::hasColumn('laporan_ndt_reports', 'test_types')) {
                $table->json('test_types')->nullable()->after('techniques');
            }

            if (!Schema::hasColumn('laporan_ndt_reports', 'material_rows')) {
                $table->json('material_rows')->nullable()->after('penetrant_applications');
            }

            if (!Schema::hasColumn('laporan_ndt_reports', 'dwell_times')) {
                $table->json('dwell_times')->nullable()->after('material_rows');
            }

            if (!Schema::hasColumn('laporan_ndt_reports', 'examiner_position')) {
                $table->string('examiner_position')->nullable()->after('examiner_name');
            }

            if (!Schema::hasColumn('laporan_ndt_reports', 'qc_inspector_position')) {
                $table->string('qc_inspector_position')->nullable()->after('qc_inspector_name');
            }

            if (!Schema::hasColumn('laporan_ndt_reports', 'owner_representative_position')) {
                $table->string('owner_representative_position')->nullable()->after('owner_representative_name');
            }

            if (!Schema::hasColumn('laporan_ndt_reports', 'surveyor_position')) {
                $table->string('surveyor_position')->nullable()->after('surveyor_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('laporan_ndt_reports', function (Blueprint $table) {
            foreach ([
                'stage_ofs',
                'examinations',
                'test_types',
                'material_rows',
                'dwell_times',
                'examiner_position',
                'qc_inspector_position',
                'owner_representative_position',
                'surveyor_position',
            ] as $column) {
                if (Schema::hasColumn('laporan_ndt_reports', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
