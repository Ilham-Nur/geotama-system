<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class LaporanNdtPhoto extends Model
{
    protected $table = 'laporan_ndt_photos';

    protected $fillable = [
        'laporan_ndt_report_id',
        'inspection_item_id',
        'type',
        'file_path',
        'original_name',
        'mime_type',
        'size',
        'sort_order',
    ];

    public const TYPE_BEFORE = 'before';
    public const TYPE_DURING = 'during';
    public const TYPE_AFTER = 'after';

    public function ndtReport(): BelongsTo
    {
        return $this->belongsTo(LaporanNdtReport::class, 'laporan_ndt_report_id');
    }

    public function inspectionItem(): BelongsTo
    {
        return $this->belongsTo(LaporanNdtInspectionItem::class, 'inspection_item_id');
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }
}
