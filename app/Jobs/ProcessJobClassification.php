<?php

namespace App\Jobs;

use App\Models\Job;
use App\Services\Jobs\JobClassificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessJobClassification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $backoff = 30;

    public function __construct(public Job $job) {}

    public function handle(JobClassificationService $classifier): void
    {
        $this->classifier->classify($this->job);
    }

    public function queue(): string
    {
        return 'job-processing';
    }
}
