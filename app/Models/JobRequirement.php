<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobRequirement extends Model
{
    protected $fillable = ['job_id', 'type', 'value', 'mandatory'];

    protected function casts(): array
    {
        return ['mandatory' => 'boolean'];
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }
}
