<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratTugas extends Model
{
    use HasFactory;

    protected $table = 'surat_tugas';

    protected $fillable = [
        'proyek_id',
        'tanggal_berangkat',
        'tanggal_kembali',
        'transportasi',
        'keterangan',
        'grand_total',
    ];

    protected $casts = [
        'tanggal_berangkat' => 'date',
        'tanggal_kembali' => 'date',
        'grand_total' => 'decimal:2',
    ];

    public function proyek()
    {
        return $this->belongsTo(Proyek::class, 'proyek_id');
    }

    public function biayaItems()
    {
        return $this->hasMany(BiayaItem::class, 'surat_tugas_id');
    }
}
