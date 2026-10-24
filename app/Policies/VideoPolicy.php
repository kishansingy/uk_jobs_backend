<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Video;

class VideoPolicy
{
    public function view(User $user, Video $video): bool
    {
        if ($video->premium_only) {
            return $user->activeSubscription?->plan?->slug !== 'free';
        }
        return true;
    }

    public function manage(User $user): bool
    {
        return $user->hasRole(['super-admin', 'admin', 'content-manager']);
    }
}
