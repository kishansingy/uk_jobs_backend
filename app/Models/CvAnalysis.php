<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CvAnalysis extends Model
{
    protected $fillable = [
        'user_id', 'document_id', 'job_id',
        'structure_feedback', 'experience_feedback', 'skills_feedback',
        'missing_evidence', 'recommendations', 'ai_provider', 'analysed_at',
    ];

    protected function casts(): array
    {
        return [
            'structure_feedback'  => 'array',
            'experience_feedback' => 'array',
            'skills_feedback'     => 'array',
            'missing_evidence'    => 'array',
            'recommendations'     => 'array',
            'analysed_at'         => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(CandidateDocument::class, 'document_id');
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }
}
