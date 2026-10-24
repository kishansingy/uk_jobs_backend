<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CvAnalysisResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'document_id'         => $this->document_id,
            'job_id'              => $this->job_id,
            'structure_feedback'  => $this->structure_feedback,
            'experience_feedback' => $this->experience_feedback,
            'skills_feedback'     => $this->skills_feedback,
            'missing_evidence'    => $this->missing_evidence,
            'recommendations'     => $this->recommendations,
            'ai_provider'         => $this->ai_provider,
            'analysed_at'         => $this->analysed_at?->toISOString(),
            'disclaimer'          => 'AI analysis is for guidance only. It does not guarantee employment outcomes or represent an official NHS assessment.',
        ];
    }
}
