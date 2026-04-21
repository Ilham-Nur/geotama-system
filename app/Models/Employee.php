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
        'emergency_contact_name',
        'emergency_contact_phone',
        'hire_date',
        'employment_status',
        'gender',
        'birth_place',
        'birth_date',
        'full_address',
        'identity_number',
        'bpjs_ketenagakerjaan_number',
        'bpjs_kesehatan_number',
        'marital_status',
        'nationality',
        'religion',
        'important_information',
        'last_education',
        'last_education_file_path',
        'last_education_file_name',
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

    public function contracts()
    {
        return $this->hasMany(EmployeeContract::class);
    }

    public function workExperiences()
    {
        return $this->hasMany(EmployeeWorkExperience::class);
    }

    public function certificates()
    {
        return $this->hasMany(EmployeeCertificate::class);
    }
}

