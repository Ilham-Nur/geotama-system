<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class NdtCertificate extends Model
{
    protected $fillable = [
        'title',
        'certificate_no',
        'type',
        'issued_at',
        'expired_at',
        'file_path',
        'preview_path',
        'is_active',
    ];

    protected $casts = [
        'issued_at' => 'date',
        'expired_at' => 'date',
        'is_active' => 'boolean',
    ];

    public function getUrlAttribute(): ?string
    {
        return $this->file_path ? Storage::url($this->file_path) : null;
    }

    public function getPreviewUrlAttribute(): ?string
    {
        return $this->preview_path ? Storage::url($this->preview_path) : null;
    }
}
