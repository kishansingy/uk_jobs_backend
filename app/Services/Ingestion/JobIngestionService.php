<?php

namespace App\Services\Ingestion;

use App\Contracts\JobSourceInterface;
use App\DTOs\JobData;
use App\Enums\JobStatus;
use App\Models\Job;
use App\Models\JobImportLog;
use App\Models\JobSource;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class JobIngestionService
{
    /** @param JobSourceInterface[] $sources */
    public function __construct(private array $sources) {}

    public function ingest(JobSource $source): JobImportLog
    {
        $log = JobImportLog::create([
            'source_id'  => $source->id,
            'status'     => 'running',
            'started_at' => now(),
        ]);

        try {
            $handler = $this->resolveHandler($source);
            $jobs    = $handler->fetch($source);

            $stats = ['created' => 0, 'updated' => 0, 'skipped' => 0];

            // Wipe existing jobs for this source before inserting fresh data.
            // Useful for scraped sources where we can't diff — just reload clean.
            $config = $source->configuration ?? [];
            if (! empty($config['wipe_before_import'])) {
                Job::where('source_id', $source->id)->delete();
                Log::info("Wiped existing jobs for source [{$source->name}] before fresh import.");
            }

            foreach ($jobs as $jobData) {
                $result = $this->upsertJob($source, $jobData);
                $stats[$result]++;
            }

            $log->update([
                'status'          => 'completed',
                'records_fetched' => count($jobs),
                'records_created' => $stats['created'],
                'records_updated' => $stats['updated'],
                'records_skipped' => $stats['skipped'],
                'completed_at'    => now(),
            ]);

            $source->update(['last_success_at' => now()]);
        } catch (Throwable $e) {
            Log::error("Job ingestion failed for source [{$source->name}]: " . $e->getMessage(), [
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            $log->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'completed_at'  => now(),
            ]);

            $source->update([
                'last_failure_at'      => now(),
                'last_failure_message' => $e->getMessage(),
            ]);
        }

        return $log;
    }

    private function upsertJob(JobSource $source, JobData $data): string
    {
        // Global dedup — same NHS job ref must never be stored twice
        $existing = Job::where('external_id', $data->externalId)->first();

        if ($existing) {
            // Skip entirely if the job is already closed/expired/removed — no point updating
            if (in_array($existing->status->value, [
                JobStatus::Closed->value,
                JobStatus::Expired->value,
                JobStatus::Removed->value,
            ])) {
                return 'skipped';
            }

            // Only update if something meaningful changed
            $closingChanged = $data->closingDate && $existing->closing_date?->toDateString() !== $data->closingDate;
            $titleChanged   = $data->title && $existing->title !== $data->title;
            $salaryChanged  = $data->salaryText && $existing->salary_text !== $data->salaryText;

            if (! $closingChanged && ! $titleChanged && ! $salaryChanged) {
                // Just touch last_seen_at so we know it's still live
                $existing->update(['last_seen_at' => now()]);
                return 'skipped';
            }

            $existing->update([
                'title'          => $data->title,
                'salary_text'    => $data->salaryText,
                'salary_min'     => $data->salaryMin,
                'salary_max'     => $data->salaryMax,
                'closing_date'   => $data->closingDate,
                'published_date' => $data->publishedDate,
                'contract_type'  => $data->contractType,
                'employment_type'=> $data->employmentType,
                'status'         => JobStatus::Updated->value,
                'last_seen_at'   => now(),
                'last_updated_at'=> now(),
            ]);
            return 'updated';
        }

        Job::create([
            'source_id'       => $source->id,
            'external_id'     => $data->externalId,
            'title'           => $data->title,
            'organisation'    => $data->organisation,
            'description'     => $data->description,
            'location'        => $data->location,
            'postcode'        => $data->postcode,
            'salary_min'      => $data->salaryMin,
            'salary_max'      => $data->salaryMax,
            'salary_text'     => $data->salaryText,
            'band'            => $data->band,
            'contract_type'   => $data->contractType,
            'employment_type' => $data->employmentType,
            'closing_date'    => $data->closingDate,
            'published_date'  => $data->publishedDate,
            'source_url'      => $data->sourceUrl,
            'raw_reference'   => $data->rawReference,
            'status'          => JobStatus::Active->value,
            'first_seen_at'   => now(),
            'last_seen_at'    => now(),
            'last_updated_at' => now(),
        ]);

        return 'created';
    }

    private function resolveHandler(JobSource $source): JobSourceInterface
    {
        foreach ($this->sources as $handler) {
            if ($handler->supports($source->type->value)) {
                return $handler;
            }
        }

        throw new \RuntimeException("No handler for source type [{$source->type->value}]");
    }
}
