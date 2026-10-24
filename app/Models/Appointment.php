<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Appointment extends Model
{
    protected $fillable = [
        'user_id', 'advisor_id', 'availability_id',
        'topic', 'notes', 'status', 'meeting_url', 'booked_at',
    ];

    protected function casts(): array
    {
        return ['booked_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function advisor(): BelongsTo
    {
        return $this->belongsTo(CareerAdvisor::class, 'advisor_id');
    }

    public function availability(): BelongsTo
    {
        return $this->belongsTo(AdvisorAvailability::class, 'availability_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(AdvisorMessage::class)->orderBy('sent_at');
    }
}
