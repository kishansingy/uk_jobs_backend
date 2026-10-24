<?php

namespace App\Http\Requests\Application;

use App\Enums\ApplicationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreApplicationRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'job_id' => ['required', 'exists:jobs,id'],
            'status' => ['required', new Enum(ApplicationStatus::class)],
            'notes'  => ['nullable', 'string', 'max:2000'],
        ];
    }
}
