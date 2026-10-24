<?php

namespace App\Policies;

use App\Models\InterviewSession;
use App\Models\User;

class InterviewSessionPolicy
{
    public function view(User $user, InterviewSession $session): bool
    {
        return $user->id === $session->user_id;
    }

    public function update(User $user, InterviewSession $session): bool
    {
        return $user->id === $session->user_id;
    }
}
