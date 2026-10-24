<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'name'                  => $this->name,
            'email'                 => $this->email,
            'phone'                 => $this->phone,
            'country'               => $this->country,
            'email_verified'        => ! is_null($this->email_verified_at),
            'onboarding_completed'  => $this->onboarding_completed,
            'roles'                 => $this->getRoleNames(),
            'created_at'            => $this->created_at?->toISOString(),
        ];
    }
}
