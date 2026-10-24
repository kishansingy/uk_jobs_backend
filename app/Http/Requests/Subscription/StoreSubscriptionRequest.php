<?php

namespace App\Http\Requests\Subscription;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriptionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'plan_id'        => ['required', 'exists:plans,id'],
            'billing_period' => ['required', 'in:monthly,annual'],
            'payment_method' => ['required', 'string', 'max:255'],
        ];
    }
}
