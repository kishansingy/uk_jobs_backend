<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                      => $this->id,
            'title'                   => $this->title,
            'organisation'            => $this->organisation,
            'description'             => $this->description,
            'location'                => $this->location,
            'postcode'                => $this->postcode,
            'salary_min'              => $this->salary_min,
            'salary_max'              => $this->salary_max,
            'salary_text'             => $this->salary_text,
            'band'                    => $this->band,
            'contract_type'           => $this->contract_type,
            'employment_type'         => $this->employment_type,
            'requirements'            => $this->requirements,
            'qualifications'          => $this->qualifications,
            'experience_required'     => $this->experience_required,
            // Only show sponsorship_information when explicitly present in source data
            'sponsorship_information' => $this->sponsorship_information,
            'closing_date'            => $this->closing_date?->toDateString(),
            'published_date'          => $this->published_date?->toDateString(),
            'status'                  => $this->status?->value,
            'source_url'              => $this->source_url,
            'profession'              => $this->whenLoaded('profession', fn() => [
                'id'                => $this->profession->id,
                'name'              => $this->profession->name,
                'registration_body' => $this->profession->registration_body,
            ]),
            'specialty'               => $this->whenLoaded('specialty', fn() => [
                'id'   => $this->specialty->id,
                'name' => $this->specialty->name,
            ]),
            'requirements_list'       => $this->whenLoaded('requirements', fn() =>
                $this->requirements->map(fn($r) => [
                    'type'      => $r->type,
                    'value'     => $r->value,
                    'mandatory' => $r->mandatory,
                ])
            ),
            'skills'                  => $this->whenLoaded('skills', fn() =>
                $this->skills->map(fn($s) => ['skill' => $s->skill, 'level' => $s->level])
            ),
            'source'                  => $this->whenLoaded('source', fn() => [
                'name' => $this->source->name,
            ]),
            // Disclaimer — never imply official NHS affiliation
            'disclaimer'              => 'This listing is sourced from ' . ($this->source?->name ?? 'an external source') . '. Always verify details on the official vacancy page before applying.',
        ];
    }
}
