<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateQualification extends Model
{
    protected $fillable = [
        'user_id', 'qualification_name', 'institution',
        'country_obtained', 'year_obtained', 'uk_recognised', 'verified',
    ];

    protected function casts(): array
    {
        return [
            'uk_recognised' => 'boolean',
            'verified'      => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
