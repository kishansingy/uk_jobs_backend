<?php

namespace App\Services\Ingestion;

use App\Contracts\JobSourceInterface;
use App\DTOs\JobData;
use App\Models\JobSource;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ApiJobSource implements JobSourceInterface
{
    public function fetch(JobSource $source): array
    {
        if (! $source->authorised) {
            throw new RuntimeException("Source [{$source->name}] is not authorised for fetching.");
        }

        $config  = $source->configuration ?? [];
        $headers = $config['headers'] ?? [];
        $params  = $config['params'] ?? [];

        if (! empty($config['api_key_header']) && ! empty($config['api_key'])) {
            $headers[$config['api_key_header']] = $config['api_key'];
        }

        $response = Http::timeout(60)
            ->withHeaders($headers)
            ->get($source->base_url, $params);

        if ($response->failed()) {
            throw new RuntimeException("API fetch failed for [{$source->name}]: " . $response->status());
        }

        $items = $this->extractItems($response->json(), $config['data_path'] ?? null);

        return array_map(
            fn(array $item) => $this->normalise($item, $config),
            $items
        );
    }

    public function supports(string $type): bool
    {
        return $type === 'api';
    }

    private function extractItems(array $data, ?string $path): array
    {
        if (! $path) {
            return is_array($data) && isset($data[0]) ? $data : [$data];
        }

        foreach (explode('.', $path) as $key) {
            $data = $data[$key] ?? [];
        }

        return is_array($data) ? $data : [];
    }

    private function normalise(array $item, array $config): JobData
    {
        $map = $config['field_map'] ?? [];

        return JobData::fromArray([
            'external_id'           => (string) ($item[$map['external_id'] ?? 'id'] ?? uniqid()),
            'title'                 => $item[$map['title'] ?? 'title'] ?? '',
            'organisation'          => $item[$map['organisation'] ?? 'organisation'] ?? '',
            'description'           => $item[$map['description'] ?? 'description'] ?? '',
            'location'              => $item[$map['location'] ?? 'location'] ?? '',
            'postcode'              => $item[$map['postcode'] ?? 'postcode'] ?? null,
            'salary_min'            => $item[$map['salary_min'] ?? 'salary_min'] ?? null,
            'salary_max'            => $item[$map['salary_max'] ?? 'salary_max'] ?? null,
            'salary_text'           => $item[$map['salary_text'] ?? 'salary_text'] ?? null,
            'band'                  => $item[$map['band'] ?? 'band'] ?? null,
            'contract_type'         => $item[$map['contract_type'] ?? 'contract_type'] ?? null,
            'employment_type'       => $item[$map['employment_type'] ?? 'employment_type'] ?? null,
            'sponsorship_information' => $item[$map['sponsorship'] ?? 'sponsorship_available'] ?? null,
            'requirements'          => $item[$map['requirements'] ?? 'requirements'] ?? null,
            'qualifications'        => $item[$map['qualifications'] ?? 'qualifications'] ?? null,
            'experience_required'   => $item[$map['experience'] ?? 'experience_required'] ?? null,
            'closing_date'          => $item[$map['closing_date'] ?? 'closing_date'] ?? null,
            'published_date'        => $item[$map['published_date'] ?? 'published_date'] ?? null,
            'source_url'            => $item[$map['source_url'] ?? 'url'] ?? null,
            'raw_data'              => $item,
        ]);
    }
}
