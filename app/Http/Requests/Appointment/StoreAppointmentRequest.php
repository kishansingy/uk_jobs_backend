<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'advisor_id'      => ['required', 'exists:career_advisors,id'],
            'availability_id' => ['required', 'exists:advisor_availabilities,id'],
            'topic'           => ['required', 'string', 'max:100'],
            'notes'           => ['nullable', 'string', 'max:1000'],
        ];
    }
}
