<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDevice extends Model
{
    protected $fillable = [
        'user_id', 'device_token', 'platform', 'app_version', 'last_seen_at', 'active',
    ];

    protected $hidden = ['device_token']; // never expose raw token in API responses

    protected function casts(): array
    {
        return [
            'active'       => 'boolean',
            'last_seen_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
