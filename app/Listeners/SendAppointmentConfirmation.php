<?php

namespace App\Listeners;

use App\Events\AppointmentBooked;
use App\Jobs\SendPushNotification;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendAppointmentConfirmation implements ShouldQueue
{
    public string $queue = 'notifications';

    public function __construct(private NotificationService $notifications) {}

    public function handle(AppointmentBooked $event): void
    {
        $appointment = $event->appointment;
        $user        = $appointment->user;

        $this->notifications->send($user, 'appointment_reminder', [
            'appointment_id' => $appointment->id,
            'topic'          => $appointment->topic,
            'advisor'        => $appointment->advisor?->display_name,
        ]);

        SendPushNotification::dispatch(
            $user,
            'Appointment Confirmed',
            "Your appointment with {$appointment->advisor?->display_name} has been confirmed.",
            ['appointment_id' => (string) $appointment->id]
        );
    }
}
