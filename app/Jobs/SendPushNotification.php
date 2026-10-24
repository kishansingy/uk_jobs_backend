<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendPushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public User $user,
        public string $title,
        public string $body,
        public array $data = []
    ) {}

    public function handle(): void
    {
        $devices = UserDevice::where('user_id', $this->user->id)
            ->where('active', true)
            ->pluck('device_token');

        if ($devices->isEmpty()) {
            return;
        }

        $serverKey = config('firebase.server_key');

        if (empty($serverKey)) {
            Log::warning('Firebase server key not configured — push notification skipped.');
            return;
        }

        foreach ($devices as $token) {
            $this->sendToFcm($token, $serverKey);
        }
    }

    private function sendToFcm(string $token, string $serverKey): void
    {
        $response = Http::withHeaders([
            'Authorization' => "key={$serverKey}",
            'Content-Type'  => 'application/json',
        ])->post('https://fcm.googleapis.com/fcm/send', [
            'to'           => $token,
            'notification' => [
                'title' => $this->title,
                'body'  => $this->body,
            ],
            'data' => $this->data,
        ]);

        if ($response->failed()) {
            Log::warning("FCM push failed for token [{$token}]: " . $response->body());

            // Deactivate invalid tokens
            if (in_array($response->json('error'), ['NotRegistered', 'InvalidRegistration'])) {
                UserDevice::where('device_token', $token)->update(['active' => false]);
            }
        }
    }

    public function queue(): string
    {
        return 'notifications';
    }
}
