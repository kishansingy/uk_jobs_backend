<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterviewFeedback extends Model
{
    protected $fillable = [
        'answer_id', 'structure_feedback', 'relevance_feedback',
        'clarity_feedback', 'overall_feedback', 'ai_provider', 'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'structure_feedback'  => 'array',
            'relevance_feedback'  => 'array',
            'clarity_feedback'    => 'array',
            'generated_at'        => 'datetime',
        ];
    }

    public function answer(): BelongsTo
    {
        return $this->belongsTo(InterviewAnswer::class, 'answer_id');
    }
}
