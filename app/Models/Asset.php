<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Asset extends Model
{
    protected $fillable = [
        'no_aset',
        'nama',
        'merek',
        'no_seri',
        'lokasi',
        'jumlah',
        'harga',
        'total',
        'file_faktur',
        'tahun',
        'remark',
        'gambar',
        'qr_token',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function getRouteKeyName(): string
    {
        return 'qr_token';
    }

    public static function generateNoAset(): string
    {
        $year = date('Y');

        $last = self::whereYear('created_at', $year)
            ->orderByDesc('id')
            ->first();

        $number = $last ? intval(substr($last->no_aset, -4)) + 1 : 1;

        return 'AST-' . $year . '-' . str_pad((string) $number, 4, '0', STR_PAD_LEFT);
    }

    public static function generateQrToken(): string
    {
        return (string) Str::uuid();
    }
}
