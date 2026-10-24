<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobMatch extends Model
{
    protected $fillable = [
        'user_id', 'job_id', 'match_score', 'match_factors',
        'explanation', 'gaps', 'method', 'calculated_at',
    ];

    protected function casts(): array
    {
        return [
            'match_score'    => 'float',
            'match_factors'  => 'array',
            'gaps'           => 'array',
            'calculated_at'  => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }
}
