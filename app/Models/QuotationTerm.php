<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationTerm extends Model
{
    protected $fillable = [
        'quotation_id',
        'name',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
}
