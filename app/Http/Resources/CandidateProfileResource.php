<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CandidateProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                     => $this->id,
            'profession'             => $this->whenLoaded('profession', fn() => [
                'id'   => $this->profession->id,
                'name' => $this->profession->name,
                'slug' => $this->profession->slug,
            ]),
            'specialty'              => $this->whenLoaded('specialty', fn() => [
                'id'   => $this->specialty->id,
                'name' => $this->specialty->name,
            ]),
            'current_country'        => $this->current_country,
            'uk_location_preference' => $this->uk_location_preference,
            'experience_years'       => $this->experience_years,
            'registration_body'      => $this->registration_body,
            // Never expose registration_number_encrypted
            'registration_status'    => $this->registration_status,
            'uk_registration_status' => $this->uk_registration_status,
            'sponsorship_required'   => $this->sponsorship_required,
            'nhs_band_preference'    => $this->nhs_band_preference,
            'professional_summary'   => $this->professional_summary,
            'onboarding_completed_at'=> $this->onboarding_completed_at?->toISOString(),
        ];
    }
}
