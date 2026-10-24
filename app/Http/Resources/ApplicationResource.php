<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'status'     => $this->status?->value,
            'status_label' => $this->status?->label(),
            'notes'      => $this->notes,
            'applied_at' => $this->applied_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'job'        => $this->whenLoaded('job', fn() => new JobResource($this->job)),
            'history'    => $this->whenLoaded('statusHistory', fn() =>
                $this->statusHistory->map(fn($h) => [
                    'status'     => $h->status?->value,
                    'label'      => $h->status?->label(),
                    'notes'      => $h->notes,
                    'changed_at' => $h->changed_at?->toISOString(),
                ])
            ),
        ];
    }
}
