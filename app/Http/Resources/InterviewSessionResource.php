<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InterviewSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'job_id'       => $this->job_id,
            'started_at'   => $this->started_at?->toISOString(),
            'completed_at' => $this->completed_at?->toISOString(),
            'answers'      => $this->whenLoaded('answers', fn() =>
                $this->answers->map(fn($a) => [
                    'id'          => $a->id,
                    'question_id' => $a->question_id,
                    'answer_text' => $a->answer_text,
                    'answered_at' => $a->answered_at?->toISOString(),
                    'feedback'    => $a->relationLoaded('feedback') && $a->feedback ? [
                        'structure_feedback' => $a->feedback->structure_feedback,
                        'relevance_feedback' => $a->feedback->relevance_feedback,
                        'clarity_feedback'   => $a->feedback->clarity_feedback,
                        'overall_feedback'   => $a->feedback->overall_feedback,
                        'disclaimer'         => 'This feedback is for practice only and is not an official NHS assessment.',
                    ] : null,
                ])
            ),
        ];
    }
}
