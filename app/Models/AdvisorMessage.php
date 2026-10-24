<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdvisorMessage extends Model
{
    protected $fillable = ['appointment_id', 'sender_id', 'message', 'read', 'sent_at'];

    protected function casts(): array
    {
        return [
            'read'    => 'boolean',
            'sent_at' => 'datetime',
        ];
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
