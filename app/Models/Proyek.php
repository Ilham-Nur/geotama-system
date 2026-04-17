<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Proyek extends Model
{
    use HasFactory;

    protected $table = 'proyek';

    protected $fillable = [
        'no_proyek',
        'permohonan_id',
        'deskripsi',
        'nominal',
        'status',
    ];


    protected $casts = [
        'nominal' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    const STATUS_PROGRESS = 'progress';
    const STATUS_REPORTING = 'reporting';
    const STATUS_ENDORSE = 'endorse';
    const STATUS_CLOSE = 'close';

    public static function statuses(): array
    {
        return [
            self::STATUS_PROGRESS,
            self::STATUS_REPORTING,
            self::STATUS_ENDORSE,
            self::STATUS_CLOSE,
        ];
    }

    public function permohonan()
    {
        return $this->belongsTo(Permohonan::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'proyek_user', 'proyek_id', 'user_id')
            ->withTimestamps();
    }

    public function isProgress(): bool
    {
        return $this->status === self::STATUS_PROGRESS;
    }

    public function isReporting(): bool
    {
        return $this->status === self::STATUS_REPORTING;
    }

    public function isEndorse(): bool
    {
        return $this->status === self::STATUS_ENDORSE;
    }

    public function isClose(): bool
    {
        return $this->status === self::STATUS_CLOSE;
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'proyek_id');
    }

    public function timesheets()
    {
        return $this->hasMany(ProyekTimesheet::class, 'proyek_id');
    }


    public static function generateProjectNo(): string
    {
        $year = date('Y');
        $prefix = "GGI-PK-$year-";

        $lastProject = self::where('no_proyek', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        if ($lastProject) {
            // ambil angka terakhir (0001)
            $lastNumber = (int) substr($lastProject->no_proyek, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
