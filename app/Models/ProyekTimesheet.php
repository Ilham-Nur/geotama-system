<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProyekTimesheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'proyek_id',
        'form_no',
        'inspection_date',
        'status',
        'remarks',
        'generated_by',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'inspection_date' => 'date',
        'verified_at' => 'datetime',
    ];

    public function proyek()
    {
        return $this->belongsTo(Proyek::class, 'proyek_id');
    }

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function uploads()
    {
        return $this->hasMany(ProyekTimesheetUpload::class)->latest();
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public static function generateFormNo(Proyek $proyek): string
    {
        $prefix = 'TS-' . ($proyek->no_proyek ?: $proyek->id) . '-' . now()->format('Ymd') . '-';

        $last = static::where('form_no', 'like', $prefix . '%')
            ->latest('id')
            ->first();

        $next = 1;

        if ($last) {
            $lastSeq = (int) substr($last->form_no, -2);
            $next = $lastSeq + 1;
        }

        return $prefix . str_pad((string) $next, 2, '0', STR_PAD_LEFT);
    }
}
