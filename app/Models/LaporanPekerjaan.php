<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaporanPekerjaan extends Model
{
    use SoftDeletes;

    protected $table = 'laporan_pekerjaan';

    protected $fillable = [
        'proyek_id',
        'item_id',
        'layanan_id',
        'tanggal_pelaksanaan',
        'action',
        'created_by',
    ];

    protected $casts = [
        'tanggal_pelaksanaan' => 'date',
    ];

    // =============================================
    //  RELASI PARENT
    // =============================================

    public function proyek(): BelongsTo
    {
        return $this->belongsTo(Proyek::class, 'proyek_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(PermohonanItem::class, 'item_id');
    }

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class, 'layanan_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // =============================================
    //  RELASI CHILD (file-file)
    // =============================================

    public function fileReport(): HasMany
    {
        return $this->hasMany(LaporanFileReport::class, 'laporan_pekerjaan_id');
    }

    public function fotoLampiran(): HasMany
    {
        return $this->hasMany(LaporanFotoLampiran::class, 'laporan_pekerjaan_id');
    }

    // =============================================
    //  HELPER / ACCESSOR
    // =============================================

    public function isDraft(): bool
    {
        return $this->action === 'draft';
    }

    public function isSubmitted(): bool
    {
        return $this->action === 'submit';
    }
}