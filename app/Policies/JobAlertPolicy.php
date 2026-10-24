<?php

namespace App\Policies;

use App\Models\JobAlert;
use App\Models\User;

class JobAlertPolicy
{
    public function view(User $user, JobAlert $alert): bool
    {
        return $user->id === $alert->user_id;
    }

    public function update(User $user, JobAlert $alert): bool
    {
        return $user->id === $alert->user_id;
    }

    public function delete(User $user, JobAlert $alert): bool
    {
        return $user->id === $alert->user_id;
    }
}
