<?php

namespace App\Http\Requests\Interview;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnswerRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'question_id' => ['required', 'exists:interview_questions,id'],
            'answer_text' => ['nullable', 'string', 'max:5000'],
            'answer_audio'=> ['nullable', 'file', 'mimes:mp3,wav,ogg,m4a', 'max:20480'],
        ];
    }
}
