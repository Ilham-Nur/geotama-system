<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermohonanItem extends Model
{
    protected $fillable = [
        'permohonan_id',
        'detail_pekerjaan',
        'tanggal_permintaan',
        'tanggal_pelaksanaan',
        'durasi',
    ];

    public function permohonan()
    {
        return $this->belongsTo(Permohonan::class);
    }

    public function layanans()
    {
        return $this->belongsToMany(
            Layanan::class,
            'permohonan_item_layanan',
            'permohonan_item_id',
            'layanan_id'
        );
    }
}
