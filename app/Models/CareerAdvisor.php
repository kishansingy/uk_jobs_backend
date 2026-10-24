<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CareerAdvisor extends Model
{
    protected $fillable = ['user_id', 'display_name', 'specialisms', 'bio', 'photo_path', 'active'];

    protected function casts(): array
    {
        return [
            'specialisms' => 'array',
            'active'      => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function availability(): HasMany
    {
        return $this->hasMany(AdvisorAvailability::class, 'advisor_id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'advisor_id');
    }
}
