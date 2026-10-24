<?php

namespace App\Policies;

use App\Models\JobSource;
use App\Models\User;

class JobSourcePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['super-admin', 'admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['super-admin', 'admin']);
    }

    public function update(User $user, JobSource $source): bool
    {
        return $user->hasRole(['super-admin', 'admin']);
    }

    public function delete(User $user, JobSource $source): bool
    {
        return $user->hasRole('super-admin');
    }
}
