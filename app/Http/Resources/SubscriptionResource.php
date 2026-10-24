<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'status'         => $this->status?->value,
            'billing_period' => $this->billing_period,
            'starts_at'      => $this->starts_at?->toISOString(),
            'ends_at'        => $this->ends_at?->toISOString(),
            'cancelled_at'   => $this->cancelled_at?->toISOString(),
            'plan'           => $this->whenLoaded('plan', fn() => [
                'id'          => $this->plan->id,
                'name'        => $this->plan->name,
                'slug'        => $this->plan->slug,
                'description' => $this->plan->description,
                'features'    => $this->plan->features->map(fn($f) => [
                    'key'   => $f->feature_key,
                    'value' => $f->feature_value,
                ]),
            ]),
        ];
    }
}
