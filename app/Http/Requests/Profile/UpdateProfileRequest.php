<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'                   => ['sometimes', 'string', 'max:255'],
            'phone'                  => ['sometimes', 'nullable', 'string', 'max:30'],
            'country'                => ['sometimes', 'nullable', 'string', 'max:100'],
            'profession_id'          => ['sometimes', 'nullable', 'exists:professions,id'],
            'specialty_id'           => ['sometimes', 'nullable', 'exists:specialties,id'],
            'current_country'        => ['sometimes', 'nullable', 'string', 'max:100'],
            'uk_location_preference' => ['sometimes', 'nullable', 'string', 'max:255'],
            'experience_years'       => ['sometimes', 'nullable', 'string', 'max:20'],
            'registration_body'      => ['sometimes', 'nullable', 'string', 'max:100'],
            'registration_status'    => ['sometimes', 'nullable', 'string', 'max:100'],
            'uk_registration_status' => ['sometimes', 'nullable', 'string', 'max:100'],
            'sponsorship_required'   => ['sometimes', 'nullable', 'boolean'],
            'nhs_band_preference'    => ['sometimes', 'nullable', 'string', 'max:10'],
            'professional_summary'   => ['sometimes', 'nullable', 'string', 'max:2000'],
        ];
    }
}
