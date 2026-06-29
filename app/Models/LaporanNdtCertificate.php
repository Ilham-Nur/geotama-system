<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class LaporanNdtCertificate extends Model
{
    protected $table = 'laporan_ndt_certificates';

    protected $fillable = [
        'laporan_ndt_report_id',
        'certificate_id',
        'certificate_title',
        'certificate_no',
        'file_path',
        'preview_path',
        'sort_order',
    ];

    public function ndtReport(): BelongsTo
    {
        return $this->belongsTo(LaporanNdtReport::class, 'laporan_ndt_report_id');
    }

    public function certificate(): BelongsTo
    {
        return $this->belongsTo(NdtCertificate::class, 'certificate_id');
    }

    public function getUrlAttribute(): ?string
    {
        return $this->file_path ? Storage::url($this->file_path) : null;
    }

    public function getPreviewUrlAttribute(): ?string
    {
        return $this->preview_path ? Storage::url($this->preview_path) : null;
    }
}
