<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'certificate_type',
        'certificate_name',
        'issued_at',
        'expired_at',
        'issuer',
        'file_path',
        'file_name',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
            'expired_at' => 'date',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
