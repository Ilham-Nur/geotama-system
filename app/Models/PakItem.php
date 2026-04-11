<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PakItem extends Model
{
    protected $fillable = [
        'pak_id',
        'category_id',
        'name',
        'description',
        'qty',
        'unit_cost',
        'total_cost',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    // =========================
    // RELATION
    // =========================
    public function pak()
    {
        return $this->belongsTo(Pak::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
