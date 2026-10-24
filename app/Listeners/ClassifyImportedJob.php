<?php

namespace App\Listeners;

use App\Events\JobImported;
use App\Jobs\ProcessJobClassification;
use App\Jobs\ProcessJobMatching;
use Illuminate\Contracts\Queue\ShouldQueue;

class ClassifyImportedJob implements ShouldQueue
{
    public string $queue = 'job-processing';

    public function handle(JobImported $event): void
    {
        ProcessJobClassification::dispatch($event->job);
        ProcessJobMatching::dispatch($event->job);
    }
}
