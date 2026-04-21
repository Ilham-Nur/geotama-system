<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeWorkExperience extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'company_name',
        'position',
        'start_year',
        'end_year',
        'certificate_file_path',
        'certificate_file_name',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
