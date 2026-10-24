<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Http\Traits\ApiResponse;
use App\Models\UserNotification;
use App\Models\UserNotificationPreference;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponse;

    public function __construct(private NotificationService $service) {}

    public function index(Request $request): JsonResponse
    {
        $notifications = UserNotification::where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        return $this->success(
            NotificationResource::collection($notifications),
            'Notifications retrieved',
            200,
            [
                'unread_count' => $this->service->unreadCount($request->user()),
                'total'        => $notifications->total(),
            ]
        );
    }

    public function markRead(Request $request, UserNotification $notification): JsonResponse
    {
        if ($notification->user_id !== $request->user()->id) {
            return $this->error('Unauthorised', 403);
        }

        $this->service->markRead($notification);
        return $this->success(null, 'Notification marked as read');
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $this->service->markAllRead($request->user());
        return $this->success(null, 'All notifications marked as read');
    }

    public function preferences(Request $request): JsonResponse
    {
        $prefs = UserNotificationPreference::where('user_id', $request->user()->id)->get();
        return $this->success($prefs);
    }

    public function updatePreferences(Request $request): JsonResponse
    {
        $request->validate([
            'preferences'                    => ['required', 'array'],
            'preferences.*.channel'          => ['required', 'in:email,push,in_app'],
            'preferences.*.notification_type'=> ['required', 'string', 'max:100'],
            'preferences.*.enabled'          => ['required', 'boolean'],
        ]);

        foreach ($request->preferences as $pref) {
            UserNotificationPreference::updateOrCreate(
                [
                    'user_id'           => $request->user()->id,
                    'channel'           => $pref['channel'],
                    'notification_type' => $pref['notification_type'],
                ],
                ['enabled' => $pref['enabled']]
            );
        }

        return $this->success(null, 'Preferences updated');
    }
}
