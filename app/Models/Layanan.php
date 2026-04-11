<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    protected $fillable = ['nama'];

    public function permohonanItems()
    {
        return $this->belongsToMany(
            PermohonanItem::class,
            'permohonan_item_layanan',
            'layanan_id',
            'permohonan_item_id'
        );
    }
}
