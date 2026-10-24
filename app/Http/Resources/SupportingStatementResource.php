<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupportingStatementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                           => $this->id,
            'title'                        => $this->title,
            'content'                      => $this->content,
            'status'                       => $this->status,
            'job_id'                       => $this->job_id,
            'items_requiring_confirmation' => $this->items_requiring_confirmation,
            'ai_provider'                  => $this->ai_provider,
            'created_at'                   => $this->created_at?->toISOString(),
            'disclaimer'                   => 'This draft is AI-generated from information you supplied. Review all content carefully before submitting. Do not submit anything you cannot personally verify.',
        ];
    }
}
