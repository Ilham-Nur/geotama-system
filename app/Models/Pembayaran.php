<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayarans';

    protected $fillable = [
        'invoice_id',
        'no_pembayaran',
        'tanggal_bayar',
        'nominal_bayar',
        'metode_pembayaran',
        'nama_pengirim',
        'bank_pengirim',
        'no_referensi',
        'bukti_pembayaran',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'nominal_bayar' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public static function generateNoPembayaran(): string
    {
        $year = date('Y');
        $prefix = "GGI-PAY-$year-";

        $last = self::where('no_pembayaran', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        if ($last) {
            $lastNumber = (int) substr($last->no_pembayaran, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
