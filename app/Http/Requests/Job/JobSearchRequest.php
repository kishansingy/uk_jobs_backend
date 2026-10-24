<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;

class JobSearchRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'keyword'              => ['nullable', 'string', 'max:255'],
            'profession_id'        => ['nullable', 'exists:professions,id'],
            'specialty_id'         => ['nullable', 'exists:specialties,id'],
            'location'             => ['nullable', 'string', 'max:255'],
            'band'                 => ['nullable', 'string', 'max:10'],
            'salary_min'           => ['nullable', 'numeric', 'min:0'],
            'salary_max'           => ['nullable', 'numeric', 'min:0'],
            'contract_type'        => ['nullable', 'string', 'max:50'],
            'sponsorship_available'=> ['nullable', 'boolean'],
            'closing_after'        => ['nullable', 'date'],
            'sort'                 => ['nullable', 'in:relevance,newest,closing_soon,salary_high,salary_low'],
            'per_page'             => ['nullable', 'integer', 'min:5', 'max:50'],
        ];
    }
}
