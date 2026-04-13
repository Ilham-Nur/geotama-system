<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'contract_type',
        'contract_number',
        'signing_date',
        'contract_start_date',
        'contract_end_date',
        'effective_date',
        'salary',
        'generated_file_path',
        'generated_file_name',
        'generated_file_size',
        'hardcopy_file_path',
        'hardcopy_file_name',
        'hardcopy_file_size',
    ];

    protected function casts(): array
    {
        return [
            'signing_date' => 'date',
            'contract_start_date' => 'date',
            'contract_end_date' => 'date',
            'effective_date' => 'date',
            'salary' => 'decimal:2',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
