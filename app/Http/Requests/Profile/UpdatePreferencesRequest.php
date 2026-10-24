<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePreferencesRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'preferred_locations'   => ['sometimes', 'nullable', 'array'],
            'preferred_locations.*' => ['string', 'max:255'],
            'contract_types'        => ['sometimes', 'nullable', 'array'],
            'contract_types.*'      => ['string', 'max:50'],
            'employment_types'      => ['sometimes', 'nullable', 'array'],
            'employment_types.*'    => ['string', 'max:50'],
            'salary_min'            => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'salary_max'            => ['sometimes', 'nullable', 'numeric', 'min:0', 'gte:salary_min'],
            'preferred_band'        => ['sometimes', 'nullable', 'string', 'max:10'],
            'keywords'              => ['sometimes', 'nullable', 'array'],
            'keywords.*'            => ['string', 'max:100'],
            'open_to_relocation'    => ['sometimes', 'boolean'],
        ];
    }
}
