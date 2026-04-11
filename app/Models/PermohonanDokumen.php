<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermohonanDokumen extends Model
{
    protected $fillable = [
        'permohonan_id',
        'jenis',
        'label',
        'file_path',
        'file_name',
    ];

    public function permohonan()
    {
        return $this->belongsTo(Permohonan::class);
    }
}
