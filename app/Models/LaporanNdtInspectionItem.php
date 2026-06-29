<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaporanNdtInspectionItem extends Model
{
    protected $table = 'laporan_ndt_inspection_items';

    protected $fillable = [
        'laporan_ndt_report_id',
        'sort_order',
        'description_master_id',
        'description',
        'code',
        'id_no',
        'diameter_mm',
        'length_mm',
        'thickness_mm',
        'result',
        'remark',
        'sketch_annotations',
    ];

    protected $casts = [
        'diameter_mm' => 'decimal:2',
        'length_mm' => 'decimal:2',
        'thickness_mm' => 'decimal:2',
        'sketch_annotations' => 'array',
    ];

    public const RESULT_ACC = 'acc';
    public const RESULT_REJECT = 'reject';
    public const RESULT_REPAIR = 'repair';

    public function ndtReport(): BelongsTo
    {
        return $this->belongsTo(LaporanNdtReport::class, 'laporan_ndt_report_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(LaporanNdtPhoto::class, 'inspection_item_id')
            ->orderBy('type')
            ->orderBy('sort_order');
    }
}
