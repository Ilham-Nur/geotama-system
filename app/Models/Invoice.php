<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'proyek_id',
        'no_invoice',
        'tanggal_invoice',
        'jenis_invoice',
        'sub_total',
        'discount',
        'tax',
        'grand_total',
        'notes',
        'file_invoice_signed',
    ];

    protected $casts = [
        'tanggal_invoice' => 'date',
        'sub_total' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function proyek()
    {
        return $this->belongsTo(Proyek::class, 'proyek_id');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public static function generateInvoiceNo(): string
    {
        $year = date('Y');
        $prefix = "GGI-INV-$year-";

        $last = self::where('no_invoice', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        if ($last) {
            $lastNumber = (int) substr($last->no_invoice, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }


    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class, 'invoice_id');
    }

    public function getTotalDibayarAttribute()
    {
        if (array_key_exists('pembayarans_sum_nominal_bayar', $this->attributes)) {
            return (float) ($this->attributes['pembayarans_sum_nominal_bayar'] ?? 0);
        }

        return (float) $this->pembayarans()->sum('nominal_bayar');
    }

    public function getSisaTagihanAttribute()
    {
        return max((float) $this->grand_total - (float) $this->total_dibayar, 0);
    }

    public function getStatusPembayaranAttribute()
    {
        if ($this->total_dibayar <= 0) {
            return 'belum_bayar';
        }

        if ($this->total_dibayar < $this->grand_total) {
            return 'sebagian';
        }

        return 'lunas';
    }
}
