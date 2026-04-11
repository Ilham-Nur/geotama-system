<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pak extends Model
{
    protected $fillable = [
        'pak_number',
        'pak_name',
        'permohonan_data',
        'project_value',
        'total_cost',
        'profit',
        'profit_percentage',
    ];

    protected $casts = [
        'permohonan_data' => 'array',
        'project_value' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'profit' => 'decimal:2',
        'profit_percentage' => 'decimal:2',
    ];

    // =========================
    // RELATION
    // =========================
    public function items()
    {
        return $this->hasMany(PakItem::class);
    }

    // =========================
    // GENERATE NUMBER
    // =========================
    public static function generateNumber()
    {
        $year = date('Y');

        $last = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $last ? intval(substr($last->pak_number, -4)) + 1 : 1;

        return 'GGI-PAK-' . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
