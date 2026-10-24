<?php

namespace App\Models;

use App\Enums\AlertFrequency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobAlert extends Model
{
    protected $fillable = ['user_id', 'name', 'filters', 'frequency', 'active', 'last_sent_at'];

    protected function casts(): array
    {
        return [
            'filters'      => 'array',
            'active'       => 'boolean',
            'frequency'    => AlertFrequency::class,
            'last_sent_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
