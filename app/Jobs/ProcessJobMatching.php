<?php

namespace App\Jobs;

use App\Models\Job;
use App\Models\User;
use App\Services\Jobs\JobMatchingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessJobMatching implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $backoff = 15;

    public function __construct(public Job $job) {}

    public function handle(JobMatchingService $matching): void
    {
        // Match the new job against all users who have completed their profile
        User::whereHas('candidateProfile', fn($q) =>
            $q->whereNotNull('profession_id')->whereNotNull('onboarding_completed_at')
        )
        ->chunk(100, function ($users) use ($matching) {
            foreach ($users as $user) {
                $matching->match($user, $this->job);
            }
        });
    }

    public function queue(): string
    {
        return 'jobs';
    }
}
