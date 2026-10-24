<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreJobSourceRequest;
use App\Http\Traits\ApiResponse;
use App\Models\JobSource;
use App\Services\AuditService;
use App\Services\Ingestion\JobIngestionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobSourceController extends Controller
{
    use ApiResponse;

    public function __construct(
        private JobIngestionService $ingestion,
        private AuditService $audit
    ) {}

    public function index(Request $request): JsonResponse
    {
        $sources = JobSource::withCount('jobs')
            ->when($request->filled('search'), fn($q) => $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('base_url', 'like', '%' . $request->search . '%')
                ->orWhere('source_identifier', 'like', '%' . $request->search . '%')
            )
            ->when($request->filled('type'),   fn($q) => $q->where('type', $request->type))
            ->when($request->filled('active'), fn($q) => $q->where('active', $request->boolean('active')))
            ->when($request->filled('authorised'), fn($q) => $q->where('authorised', $request->boolean('authorised')))
            ->orderByDesc('created_at')
            ->get();

        return $this->success($sources);
    }

    public function store(StoreJobSourceRequest $request): JsonResponse
    {
        $source = JobSource::create($request->validated());
        $this->audit->log('job_source.created', $request->user()->id, $source);
        return $this->created($source);
    }

    public function update(StoreJobSourceRequest $request, JobSource $jobSource): JsonResponse
    {
        $jobSource->update($request->validated());
        $this->audit->log('job_source.updated', $request->user()->id, $jobSource);
        return $this->success($jobSource->fresh(), 'Source updated');
    }

    public function destroy(Request $request, JobSource $jobSource): JsonResponse
    {
        $jobSource->delete();
        $this->audit->log('job_source.deleted', $request->user()->id, $jobSource);
        return $this->noContent();
    }

    public function triggerImport(Request $request, JobSource $jobSource): JsonResponse
    {
        if (! $jobSource->authorised) {
            return $this->error('Source is not authorised. Verify permissions before importing.', 422);
        }

        \App\Jobs\FetchJobsFromSource::dispatch($jobSource);

        return $this->success(['queued_at' => now()], 'Import queued — check logs in a few minutes');
    }

    /**
     * Test scrape: runs page 1 only, synchronously, returns raw parsed jobs.
     * Use this to validate scraping works before running a full import.
     */
    public function testScrape(JobSource $jobSource): JsonResponse
    {
        // Temporarily override max_pages to 1 for a quick test
        $testSource = clone $jobSource;
        $config = $testSource->configuration ?? [];
        $config['max_pages'] = 1;
        $testSource->setRawAttributes(array_merge($testSource->getRawOriginal(), []), true);

        try {
            // Directly call NHS scraper with max_pages=1
            $scraper = new \App\Services\Ingestion\NhsJobsSource();
            // Temporarily set max_pages=1 on a cloned config
            $original = $jobSource->configuration;
            $jobSource->configuration = array_merge($original ?? [], ['max_pages' => 1]);

            $jobs = $scraper->fetch($jobSource);

            $jobSource->configuration = $original; // restore

            return $this->success([
                'jobs_found' => count($jobs),
                'sample'     => array_slice(
                    array_map(fn($j) => [
                        'external_id'   => $j->externalId,
                        'title'         => $j->title,
                        'organisation'  => $j->organisation,
                        'location'      => $j->location,
                        'salary_text'   => $j->salaryText,
                        'contract_type' => $j->contractType,
                        'closing_date'  => $j->closingDate?->toDateString(),
                        'source_url'    => $j->sourceUrl,
                    ], $jobs),
                    0, 5  // show first 5
                ),
            ], 'Test scrape complete');
        } catch (\Throwable $e) {
            return $this->error('Scrape failed: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine(), 500);
        }
    }

    public function importLogs(JobSource $jobSource): JsonResponse
    {
        $logs = $jobSource->importLogs()
            ->orderByDesc('started_at')
            ->take(50)
            ->get();

        return $this->success($logs);
    }
}
