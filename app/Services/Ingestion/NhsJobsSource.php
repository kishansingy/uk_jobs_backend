<?php

namespace App\Services\Ingestion;

use App\Contracts\JobSourceInterface;
use App\DTOs\JobData;
use App\Models\JobSource;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Scrapes NHS Jobs search results HTML — all pages automatically.
 *
 * Required config on the JobSource:
 *   keyword  — e.g. "nurse"
 *   location — e.g. "London"
 *
 * Optional config:
 *   country         — default "GB-ENG"
 *   distance        — miles radius, default "" (only in location)
 *   working_pattern — e.g. "full-time"
 *   contract_type   — e.g. "Permanent"
 *   max_pages       — hard cap (0 = no cap, scrape everything), default 0
 *   wipe_before_import — handled by JobIngestionService (set true on source config)
 */
class NhsJobsSource implements JobSourceInterface
{
    private const BASE    = 'https://www.jobs.nhs.uk';
    private const SEARCH  = '/candidate/search/results';

    private const HEADERS = [
        'User-Agent'               => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
        'Accept'                   => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language'          => 'en-GB,en;q=0.9',
        'Connection'               => 'keep-alive',
        'Upgrade-Insecure-Requests'=> '1',
    ];

    public function fetch(JobSource $source): array
    {
        $config   = $source->configuration ?? [];
        $keyword  = $config['keyword']  ?? '';
        $location = $config['location'] ?? '';
        $country  = $config['country']  ?? 'GB-ENG';
        $hardCap  = (int) ($config['max_pages'] ?? 5); // default 5 pages; 0 = no cap

        $baseParams = array_filter([
            'keyword'        => $keyword,
            'location'       => $location,
            'country'        => $country,
            'distance'       => $config['distance']        ?? '',
            'workingPattern' => $config['working_pattern'] ?? '',
            'contractType'   => $config['contract_type']   ?? '',
            'language'       => 'en',
        ], fn($v) => $v !== '' && $v !== null);

        // ── Page 1: discover total pages ──────────────────────────────────────
        $firstHtml  = $this->fetchPage($baseParams);
        $firstCrawl = new Crawler($firstHtml);
        $totalPages = $this->detectTotalPages($firstCrawl);

        // 0 = no cap, otherwise limit to hardCap
        $maxPages = $hardCap > 0 ? min($hardCap, $totalPages) : $totalPages;

        Log::info("NHS Jobs [{$source->name}]: {$totalPages} total pages, scraping {$maxPages}.");

        $jobs = $this->parseJobs($firstCrawl, $keyword, $location, $country);

        // ── Pages 2 → N ───────────────────────────────────────────────────────
        for ($page = 2; $page <= $maxPages; $page++) {
            usleep(600_000); // 0.6s polite delay to avoid rate limiting

            try {
                $html    = $this->fetchPage(array_merge($baseParams, ['page' => $page]));
                $crawler = new Crawler($html);
                $found   = $this->parseJobs($crawler, $keyword, $location, $country);
                $jobs    = array_merge($jobs, $found);

                Log::info("NHS Jobs [{$source->name}]: page {$page}/{$maxPages} — " . count($found) . " jobs.");
            } catch (\Throwable $e) {
                // Stop gracefully and return what we have so far
                Log::warning("NHS Jobs [{$source->name}]: stopped at page {$page} — " . $e->getMessage());
                break;
            }
        }

        return $jobs;
    }

    public function supports(string $type): bool
    {
        return $type === 'nhs_jobs';
    }

    // ── Private ───────────────────────────────────────────────────────────────

    private function fetchPage(array $params): string
    {
        $response = Http::withHeaders(self::HEADERS)
            ->timeout(30)
            ->get(self::BASE . self::SEARCH, $params);

        if ($response->failed()) {
            throw new RuntimeException(
                "NHS Jobs scrape failed: HTTP {$response->status()}"
            );
        }

        return $response->body();
    }

    /**
     * Extract total page count from pagination text "Page X of N".
     */
    private function detectTotalPages(Crawler $crawler): int
    {
        // "Page 2 of 49" appears in both prev and next pagination links
        $text = '';

        $crawler->filter('.nhsuk-pagination__page')->each(function (Crawler $node) use (&$text) {
            $t = trim($node->text(''));
            if (str_contains($t, ' of ')) {
                $text = $t;
            }
        });

        if ($text && preg_match('/of\s+(\d+)/i', $text, $m)) {
            return (int) $m[1];
        }

        // Fallback: if no pagination exists, it's a single page
        return 1;
    }

    /**
     * Parse all job cards from a results page.
     *
     * @return JobData[]
     */
    private function parseJobs(Crawler $crawler, string $keyword, string $location, string $country): array
    {
        $jobs = [];

        $crawler->filter('li[data-test="search-result"]')->each(
            function (Crawler $li) use (&$jobs, $keyword, $location, $country) {
                try {
                    $jobs[] = $this->parseCard($li, $keyword, $location, $country);
                } catch (\Throwable $e) {
                    Log::warning('NHS Jobs: skipped malformed card — ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
                }
            }
        );

        return $jobs;
    }

    private function parseCard(Crawler $li, string $keyword, string $location, string $country): JobData
    {
        // Title + href
        $titleNode = $li->filter('[data-test="search-result-job-title"]');
        $title     = trim($titleNode->text(''));
        $href      = $titleNode->attr('href') ?? '';

        // Job reference from /candidate/jobadvert/C9363-26-1195
        preg_match('#/candidate/jobadvert/([^?/]+)#', $href, $m);
        $jobRef  = $m[1] ?? uniqid('nhs_');

        $sourceUrl = self::BASE . '/candidate/jobadvert/' . $jobRef
            . '?country=' . $country
            . '&keyword=' . urlencode($keyword)
            . '&location=' . urlencode($location)
            . '&language=en';

        // Employer name + location string
        $h3       = $li->filter('[data-test="search-result-location"] h3');
        $orgRaw   = $h3->count() ? $h3->text('') : '';
        $lines    = array_values(array_filter(array_map('trim', preg_split('/\r?\n/', $orgRaw))));
        $org      = $lines[0] ?? 'NHS';
        $locText  = $lines[1] ?? $location;

        // Structured fields
        $salaryText  = $this->strong($li, 'search-result-salary');
        $pubDate     = $this->strong($li, 'search-result-publicationDate');
        $closeDate   = $this->strong($li, 'search-result-closingDate');
        $contractType   = $this->strong($li, 'search-result-jobType');
        $workPattern    = $this->strong($li, 'search-result-workingPattern');

        [$salMin, $salMax] = $this->parseSalary($salaryText);

        // Postcode from location e.g. "London E1 1FR"
        preg_match('/([A-Z]{1,2}\d{1,2}[A-Z]?\s*\d[A-Z]{2})/i', $locText, $pc);

        return JobData::fromArray([
            'external_id'    => $jobRef,
            'title'          => $title ?: 'Untitled',
            'organisation'   => $org,
            'description'    => $title, // full description needs a separate page fetch
            'location'       => $locText,
            'postcode'       => $pc[1] ?? null,
            'salary_text'    => $salaryText ?: null,
            'salary_min'     => $salMin,
            'salary_max'     => $salMax,
            'contract_type'  => $contractType ?: null,
            'employment_type'=> $workPattern ?: null,
            'published_date' => $pubDate   ? $this->toDate($pubDate)   : null,
            'closing_date'   => $closeDate ? $this->toDate($closeDate) : null,
            'source_url'     => $sourceUrl,
            'raw_data'       => ['job_ref' => $jobRef, 'keyword' => $keyword, 'location' => $location],
        ]);
    }

    /** Get the <strong> text inside a [data-test="X"] element. */
    private function strong(Crawler $li, string $dataTest): string
    {
        $node = $li->filter("[data-test=\"{$dataTest}\"] strong");
        return $node->count() ? trim($node->text('')) : '';
    }

    /** Parse "£28,407 to £34,581a year" → [28407.0, 34581.0] */
    private function parseSalary(string $text): array
    {
        preg_match_all('/£([\d,]+)/', $text, $m);
        $nums = array_map(fn($n) => (float) str_replace(',', '', $n), $m[1] ?? []);
        return [$nums[0] ?? null, $nums[1] ?? ($nums[0] ?? null)];
    }

    /** Parse "16 October 2026" → "2026-10-16" */
    private function toDate(string $text): ?string
    {
        try {
            return \Carbon\Carbon::parse(trim($text))->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }
}
