<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProyekTimesheetUpload extends Model
{
    use HasFactory;

    protected $fillable = [
        'proyek_timesheet_id',
        'proyek_id',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'version_no',
        'notes',
        'uploaded_by',
    ];

    public function timesheet()
    {
        return $this->belongsTo(ProyekTimesheet::class, 'proyek_timesheet_id');
    }

    public function proyek()
    {
        return $this->belongsTo(Proyek::class, 'proyek_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
