<?php

namespace App\Jobs;

use App\Models\JobSource;
use App\Services\Ingestion\JobIngestionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchJobsFromSource implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 60; // seconds

    public function __construct(public JobSource $source) {}

    public function handle(JobIngestionService $ingestion): void
    {
        $ingestion->ingest($this->source);
    }

    public function queue(): string
    {
        return 'job-import';
    }

    public function tags(): array
    {
        return ['source:' . $this->source->source_identifier];
    }
}
