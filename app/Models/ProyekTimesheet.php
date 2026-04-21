<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProyekTimesheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'proyek_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'durasi_hari',
        'file_path',
        'original_name',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'durasi_hari' => 'integer',
    ];

    public function proyek()
    {
        return $this->belongsTo(Proyek::class, 'proyek_id');
    }
}
