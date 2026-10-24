<?php

namespace App\Jobs;

use App\Enums\JobStatus;
use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CloseExpiredJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // Mark jobs closing within 3 days as closing_soon
        Job::whereIn('status', [JobStatus::Active->value, JobStatus::Updated->value])
            ->whereBetween('closing_date', [now()->toDateString(), now()->addDays(3)->toDateString()])
            ->update(['status' => JobStatus::ClosingSoon->value]);

        // Mark past-closing-date jobs as closed
        Job::whereIn('status', [
            JobStatus::Active->value,
            JobStatus::Updated->value,
            JobStatus::ClosingSoon->value,
        ])
        ->where('closing_date', '<', now()->toDateString())
        ->update(['status' => JobStatus::Closed->value]);

        // Mark jobs not seen for 30+ days as expired
        Job::whereIn('status', [JobStatus::Closed->value])
            ->where('last_seen_at', '<', now()->subDays(30))
            ->update(['status' => JobStatus::Expired->value]);
    }

    public function queue(): string
    {
        return 'job-processing';
    }
}
