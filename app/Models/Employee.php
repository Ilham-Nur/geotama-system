<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_code',
        'full_name',
        'position',
        'phone',
        'hire_date',
        'employment_status',
        'gender',
        'birth_place',
        'birth_date',
        'full_address',
        'identity_number',
        'marital_status',
        'nationality',
        'religion',
        'photo_path',
    ];

    protected function casts(): array
    {
        return [
            'hire_date' => 'date',
            'birth_date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function documents()
    {
        return $this->hasMany(EmployeeDocument::class);
    }
}
