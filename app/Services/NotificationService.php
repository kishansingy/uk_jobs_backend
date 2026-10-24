<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserNotification;
use App\Models\UserNotificationPreference;

class NotificationService
{
    /**
     * Store an in-app notification and respect user preferences.
     */
    public function send(User $user, string $type, array $data = []): void
    {
        if ($this->isEnabled($user, 'in_app', $type)) {
            UserNotification::create([
                'user_id' => $user->id,
                'type'    => $type,
                'data'    => $data,
                'read'    => false,
            ]);
        }
    }

    public function markRead(UserNotification $notification): void
    {
        $notification->update(['read' => true, 'read_at' => now()]);
    }

    public function markAllRead(User $user): void
    {
        UserNotification::where('user_id', $user->id)
            ->where('read', false)
            ->update(['read' => true, 'read_at' => now()]);
    }

    public function unreadCount(User $user): int
    {
        return UserNotification::where('user_id', $user->id)
            ->where('read', false)
            ->count();
    }

    private function isEnabled(User $user, string $channel, string $type): bool
    {
        $pref = UserNotificationPreference::where('user_id', $user->id)
            ->where('channel', $channel)
            ->where('notification_type', $type)
            ->first();

        // Default to enabled if no preference set
        return $pref === null || $pref->enabled;
    }
}
