<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlertResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'filters'      => $this->filters,
            'frequency'    => $this->frequency?->value,
            'active'       => $this->active,
            'last_sent_at' => $this->last_sent_at?->toISOString(),
            'created_at'   => $this->created_at?->toISOString(),
        ];
    }
}
