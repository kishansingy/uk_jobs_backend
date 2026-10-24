<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdvisorAvailability extends Model
{
    protected $fillable = ['advisor_id', 'available_date', 'start_time', 'end_time', 'booked'];

    protected function casts(): array
    {
        return [
            'available_date' => 'date',
            'booked'         => 'boolean',
        ];
    }

    public function advisor(): BelongsTo
    {
        return $this->belongsTo(CareerAdvisor::class, 'advisor_id');
    }
}
