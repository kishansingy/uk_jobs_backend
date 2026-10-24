<?php

namespace App\Listeners;

use App\Events\ApplicationStatusChanged;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyUserOnApplicationChange implements ShouldQueue
{
    public string $queue = 'notifications';

    public function __construct(private NotificationService $notifications) {}

    public function handle(ApplicationStatusChanged $event): void
    {
        $this->notifications->send(
            $event->application->user,
            'application_status_changed',
            [
                'application_id' => $event->application->id,
                'job_title'      => $event->application->job?->title,
                'old_status'     => $event->oldStatus,
                'new_status'     => $event->newStatus,
            ]
        );
    }
}
