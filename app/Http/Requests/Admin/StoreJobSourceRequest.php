<?php

namespace App\Http\Requests\Admin;

use App\Enums\JobSourceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreJobSourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(['super-admin', 'admin']);
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PATCH') || $this->isMethod('PUT');
        $required = $isUpdate ? 'sometimes' : 'required';

        // On update, ignore the unique check for the current record
        $uniqueRule = $isUpdate
            ? 'unique:job_sources,source_identifier,' . $this->route('jobSource')?->id
            : 'unique:job_sources,source_identifier';

        return [
            'name'                   => [$required, 'string', 'max:255'],
            'type'                   => [$required, new Enum(JobSourceType::class)],
            'base_url'               => ['nullable', 'url', 'max:500'],
            'source_identifier'      => [$required, 'string', 'max:100', $uniqueRule],
            'active'                 => ['boolean'],
            'authorised'             => ['boolean'],
            'fetch_method'           => ['in:pull,push'],
            'fetch_interval_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'configuration'          => ['nullable', 'array'],
            'permission_status'      => ['nullable', 'string', 'max:100'],
            'terms_reference'        => ['nullable', 'string', 'max:500'],
        ];
    }
}
