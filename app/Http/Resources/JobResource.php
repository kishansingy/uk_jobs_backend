<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                     => $this->id,
            'title'                  => $this->title,
            'organisation'           => $this->organisation,
            'location'               => $this->location,
            'postcode'               => $this->postcode,
            'salary_min'             => $this->salary_min,
            'salary_max'             => $this->salary_max,
            'salary_text'            => $this->salary_text,
            'band'                   => $this->band,
            'contract_type'          => $this->contract_type,
            'employment_type'        => $this->employment_type,
            'closing_date'           => $this->closing_date?->toDateString(),
            'published_date'         => $this->published_date?->toDateString(),
            'status'                 => $this->status?->value,
            'source_url'             => $this->source_url,
            // sponsorship_information only shown when explicitly present — never inferred
            'sponsorship_available'  => ! empty($this->sponsorship_information),
            'profession'             => $this->whenLoaded('profession', fn() => [
                'id'   => $this->profession->id,
                'name' => $this->profession->name,
            ]),
            'specialty'              => $this->whenLoaded('specialty', fn() => [
                'id'   => $this->specialty->id,
                'name' => $this->specialty->name,
            ]),
            'source'                 => $this->whenLoaded('source', fn() => [
                'name' => $this->source->name,
            ]),
            // match only included when loaded by job detail endpoint
            'match'                  => $this->whenLoaded('matches', function () use ($request) {
                $match = $this->matches->firstWhere('user_id', $request->user()?->id);
                return $match ? [
                    'score'       => $match->match_score,
                    'explanation' => $match->explanation,
                    'gaps'        => $match->gaps,
                ] : null;
            }),
        ];
    }
}
