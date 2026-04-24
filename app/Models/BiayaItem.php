<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaItem extends Model
{
    protected $table = 'biaya_item';

    protected $fillable = [
        'surat_tugas_id',
        'deskripsi',
        'qty',
        'total',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function suratTugas()
    {
        return $this->belongsTo(SuratTugas::class, 'surat_tugas_id');
    }
}
