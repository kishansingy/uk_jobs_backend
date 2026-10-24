<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function view(User $user, Appointment $appointment): bool
    {
        return $user->id === $appointment->user_id
            || $user->id === $appointment->advisor?->user_id
            || $user->hasRole(['super-admin', 'admin']);
    }

    public function update(User $user, Appointment $appointment): bool
    {
        return $user->id === $appointment->user_id
            || $user->id === $appointment->advisor?->user_id;
    }
}
