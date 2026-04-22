<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permohonan extends Model
{
    protected $fillable = [
        'nomor',
        'client_id',
        'nama_perusahaan',
        'alamat',
        'nama_pic',
        'no_telp',
        'email',
        'testuji',
        'testuji_external_keterangan',
        'lokasi',
        'nama_proyek',
        'permintaan_khusus',
    ];

    protected $appends = ['status'];

    public function items()
    {
        return $this->hasMany(PermohonanItem::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function proyek()
    {
        return $this->hasOne(Proyek::class);
    }

    public function dokumens()
    {
        return $this->hasMany(PermohonanDokumen::class);
    }

    public function getStatusAttribute()
    {
        if ($this->items->isEmpty()) {
            return 'OPEN';
        }

        foreach ($this->items as $item) {
            if (empty($item->tanggal_pelaksanaan) || empty($item->durasi)) {
                return 'OPEN';
            }
        }

        return 'CLOSE';
    }

    public static function generateNomor()
    {
        $year = date('Y');

        $last = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;

        if ($last) {
            $lastSequence = (int) substr($last->nomor, -4);
            $nextNumber = $lastSequence + 1;
        }

        return 'GGI-FP-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
