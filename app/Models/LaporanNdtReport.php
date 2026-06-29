<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LaporanNdtReport extends Model
{
    use SoftDeletes;

    protected $table = 'laporan_ndt_reports';

    protected $fillable = [
        'laporan_pekerjaan_id',
        'report_no',
        'service_code',
        'service_sequence',
        'part_no',
        'description',
        'material',
        'temperature',
        'procedure_id',
        'criteria_id',
        'testing_standard_id',
        'surface_conditions',
        'preparation_conditions',
        'stage_ofs',
        'examinations',
        'techniques',
        'test_types',
        'penetrant_applications',
        'material_rows',
        'dwell_times',
        'blacklight_intensity',
        'examiner_name',
        'examiner_position',
        'qc_inspector_name',
        'qc_inspector_position',
        'owner_representative_name',
        'owner_representative_position',
        'surveyor_name',
        'surveyor_position',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'surface_conditions' => 'array',
        'preparation_conditions' => 'array',
        'stage_ofs' => 'array',
        'examinations' => 'array',
        'techniques' => 'array',
        'test_types' => 'array',
        'penetrant_applications' => 'array',
        'material_rows' => 'array',
        'dwell_times' => 'array',
    ];

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMIT = 'submit';

    public const SERVICE_MT = 'MT';
    public const SERVICE_PT = 'PT';

    public function laporanPekerjaan(): BelongsTo
    {
        return $this->belongsTo(LaporanPekerjaan::class, 'laporan_pekerjaan_id');
    }

    public function inspectionItems(): HasMany
    {
        return $this->hasMany(LaporanNdtInspectionItem::class, 'laporan_ndt_report_id')
            ->orderBy('sort_order');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(LaporanNdtPhoto::class, 'laporan_ndt_report_id')
            ->orderBy('sort_order');
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(LaporanNdtCertificate::class, 'laporan_ndt_report_id')
            ->orderBy('sort_order');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isSubmitted(): bool
    {
        return $this->status === self::STATUS_SUBMIT;
    }
}
