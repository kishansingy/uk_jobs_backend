<?php

namespace App\Jobs;

use App\Enums\AlertFrequency;
use App\Models\JobAlert;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class SendJobAlertNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private string $frequency) {}

    public function handle(NotificationService $notifications): void
    {
        JobAlert::where('active', true)
            ->where('frequency', $this->frequency)
            ->with('user')
            ->chunk(100, function ($alerts) use ($notifications) {
                foreach ($alerts as $alert) {
                    $jobs = $this->findMatchingJobs($alert);

                    if ($jobs->isEmpty()) {
                        continue;
                    }

                    $notifications->send($alert->user, 'new_matching_job', [
                        'alert_name' => $alert->name,
                        'job_count'  => $jobs->count(),
                        'job_ids'    => $jobs->pluck('id')->take(5)->toArray(),
                    ]);

                    $alert->update(['last_sent_at' => now()]);
                }
            });
    }

    private function findMatchingJobs(JobAlert $alert): \Illuminate\Support\Collection
    {
        $filters = $alert->filters ?? [];

        $query = \App\Models\Job::query()->visible();

        if (! empty($filters['profession_id'])) {
            $query->where('profession_id', $filters['profession_id']);
        }
        if (! empty($filters['specialty_id'])) {
            $query->where('specialty_id', $filters['specialty_id']);
        }
        if (! empty($filters['location'])) {
            $query->where('location', 'like', '%' . $filters['location'] . '%');
        }
        if (! empty($filters['band'])) {
            $query->where('band', $filters['band']);
        }
        if (! empty($filters['keywords'])) {
            foreach ((array) $filters['keywords'] as $kw) {
                $query->where(fn($q) =>
                    $q->where('title', 'like', "%{$kw}%")
                      ->orWhere('description', 'like', "%{$kw}%")
                );
            }
        }

        // Only send jobs published since last alert was sent
        if ($alert->last_sent_at) {
            $query->where('published_date', '>=', $alert->last_sent_at->toDateString());
        } else {
            $query->where('published_date', '>=', now()->subDays(7)->toDateString());
        }

        return $query->select('id', 'title')->limit(20)->get();
    }

    public function queue(): string
    {
        return 'notifications';
    }
}
