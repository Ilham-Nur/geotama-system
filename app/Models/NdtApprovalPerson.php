<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NdtApprovalPerson extends Model
{
    protected $fillable = ['role', 'name', 'position', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public const ROLES = [
        'examiner' => 'Examiner ASNT II',
        'qc_inspector' => 'QC Inspector',
        'owner_representative' => 'Owner Representative',
        'surveyor' => 'Surveyor',
    ];

}
