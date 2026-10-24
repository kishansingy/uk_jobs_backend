<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class StoreQualificationRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'qualification_name' => ['required', 'string', 'max:255'],
            'institution'        => ['nullable', 'string', 'max:255'],
            'country_obtained'   => ['nullable', 'string', 'max:100'],
            'year_obtained'      => ['nullable', 'digits:4', 'integer', 'min:1950', 'max:' . date('Y')],
            'uk_recognised'      => ['boolean'],
        ];
    }
}
