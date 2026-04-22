<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'nama_perusahaan',
        'alamat',
        'nama_pic',
        'no_telp',
        'email',
    ];

    public function permohonans()
    {
        return $this->hasMany(Permohonan::class);
    }
}
