<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NdtAcceptanceCriterion extends Model
{
    protected $table = 'ndt_acceptance_criteria';

    protected $fillable = ['code', 'name', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
