<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportingStatement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'job_id', 'title', 'content', 'status',
        'ai_provider', 'items_requiring_confirmation',
    ];

    protected function casts(): array
    {
        return [
            'items_requiring_confirmation' => 'array',
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
