<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class LaporanFotoLampiran extends Model
{
    protected $table = 'laporan_foto_lampiran';

    protected $fillable = [
        'laporan_pekerjaan_id',
        'nama_file',
        'path',
        'mime_type',
        'size',
    ];

    // =============================================
    //  RELASI
    // =============================================

    public function laporan(): BelongsTo
    {
        return $this->belongsTo(LaporanPekerjaan::class, 'laporan_pekerjaan_id');
    }

    // =============================================
    //  ACCESSOR
    // =============================================

    /** URL publik untuk diakses di view / tag <img> */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }

    /** Ukuran file dalam format manusia-baca */
    public function getSizeReadableAttribute(): string
    {
        $bytes = $this->size;

        if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return number_format($bytes / 1024, 2) . ' KB';

        return $bytes . ' B';
    }
}