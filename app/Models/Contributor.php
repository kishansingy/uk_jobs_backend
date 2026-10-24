<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contributor extends Model
{
    protected $fillable = [
        'user_id', 'name', 'profession', 'specialty', 'experience_summary',
        'role', 'organisation', 'biography', 'photo_path', 'verification_status', 'active',
    ];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function videos(): HasMany
    {
        return $this->hasMany(Video::class);
    }
}
