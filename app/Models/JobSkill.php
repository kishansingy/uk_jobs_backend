<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobSkill extends Model
{
    protected $fillable = ['job_id', 'skill', 'level'];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }
}
