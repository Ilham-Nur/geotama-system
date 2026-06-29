<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class NdtInspectionDescription extends Model
{
    protected $fillable = ['name', 'description', 'sketch_path', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function getSketchUrlAttribute(): ?string
    {
        return $this->sketch_path ? Storage::url($this->sketch_path) : null;
    }
}
