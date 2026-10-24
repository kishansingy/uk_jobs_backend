<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class InterviewAnswer extends Model
{
    protected $fillable = ['session_id', 'question_id', 'answer_text', 'answer_audio_path', 'answered_at'];

    protected function casts(): array
    {
        return ['answered_at' => 'datetime'];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(InterviewSession::class, 'session_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(InterviewQuestion::class, 'question_id');
    }

    public function feedback(): HasOne
    {
        return $this->hasOne(InterviewFeedback::class, 'answer_id');
    }
}
