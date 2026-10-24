<?php

namespace App\Http\Requests\Alert;

use App\Enums\AlertFrequency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreAlertRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'                   => ['required', 'string', 'max:255'],
            'frequency'              => ['required', new Enum(AlertFrequency::class)],
            'active'                 => ['boolean'],
            'filters'                => ['required', 'array'],
            'filters.profession_id'  => ['nullable', 'exists:professions,id'],
            'filters.specialty_id'   => ['nullable', 'exists:specialties,id'],
            'filters.location'       => ['nullable', 'string', 'max:255'],
            'filters.band'           => ['nullable', 'string', 'max:10'],
            'filters.salary_min'     => ['nullable', 'numeric'],
            'filters.salary_max'     => ['nullable', 'numeric'],
            'filters.sponsorship'    => ['nullable', 'boolean'],
            'filters.keywords'       => ['nullable', 'array'],
            'filters.keywords.*'     => ['string', 'max:100'],
        ];
    }
}
