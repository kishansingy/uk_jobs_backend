<?php

namespace App\Policies;

use App\Models\SupportingStatement;
use App\Models\User;

class SupportingStatementPolicy
{
    public function view(User $user, SupportingStatement $statement): bool
    {
        return $user->id === $statement->user_id;
    }

    public function update(User $user, SupportingStatement $statement): bool
    {
        return $user->id === $statement->user_id;
    }

    public function delete(User $user, SupportingStatement $statement): bool
    {
        return $user->id === $statement->user_id;
    }
}
