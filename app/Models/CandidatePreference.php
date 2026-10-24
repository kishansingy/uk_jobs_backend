<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidatePreference extends Model
{
    protected $fillable = [
        'user_id', 'preferred_locations', 'contract_types', 'employment_types',
        'salary_min', 'salary_max', 'preferred_band', 'keywords', 'open_to_relocation',
    ];

    protected function casts(): array
    {
        return [
            'preferred_locations'   => 'array',
            'contract_types'        => 'array',
            'employment_types'      => 'array',
            'keywords'              => 'array',
            'salary_min'            => 'float',
            'salary_max'            => 'float',
            'open_to_relocation'    => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
