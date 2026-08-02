<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $fillable = [
        'no_quo',
        'tanggal',
        'client_id',
        'discount',
        'grand_total_quo',
        'qr_code_path',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'discount' => 'decimal:2',
        'grand_total_quo' => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function terms()
    {
        return $this->hasMany(QuotationTerm::class);
    }

    public static function generateNoQuotation(): string
    {
        $year = date('Y');
        $prefix = "GGI-QUO-$year-";

        $last = self::where('no_quo', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        $nextNumber = $last ? ((int) substr($last->no_quo, -4)) + 1 : 1;

        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
