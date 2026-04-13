<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'document_label',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
