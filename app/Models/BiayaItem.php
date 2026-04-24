<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiayaItem extends Model
{
    use HasFactory;

    protected $table = 'biaya_item';

    protected $fillable = [
        'surat_tugas_id',
        'deskripsi',
        'qty',
        'total',
    ];

    protected $casts = [
        'qty' => 'integer',
        'total' => 'decimal:2',
    ];

    public function suratTugas()
    {
        return $this->belongsTo(SuratTugas::class, 'surat_tugas_id');
    }
}
