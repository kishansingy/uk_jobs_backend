<?php

namespace App\Policies;

use App\Models\CandidateDocument;
use App\Models\User;

class CandidateDocumentPolicy
{
    public function view(User $user, CandidateDocument $document): bool
    {
        return $user->id === $document->user_id
            || $user->hasRole(['super-admin', 'admin']);
    }

    public function delete(User $user, CandidateDocument $document): bool
    {
        return $user->id === $document->user_id;
    }
}
