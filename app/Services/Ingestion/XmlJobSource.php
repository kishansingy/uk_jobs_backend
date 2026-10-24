<?php

namespace App\Services\Ingestion;

use App\Contracts\JobSourceInterface;
use App\DTOs\JobData;
use App\Models\JobSource;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use SimpleXMLElement;

class XmlJobSource implements JobSourceInterface
{
    public function fetch(JobSource $source): array
    {
        if (! $source->authorised) {
            throw new RuntimeException("Source [{$source->name}] is not authorised.");
        }

        $config   = $source->configuration ?? [];
        $headers  = $config['headers'] ?? [];

        $response = Http::timeout(60)->withHeaders($headers)->get($source->base_url);

        if ($response->failed()) {
            throw new RuntimeException("XML fetch failed for [{$source->name}]: " . $response->status());
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($response->body());

        if ($xml === false) {
            throw new RuntimeException("Failed to parse XML from [{$source->name}]");
        }

        $itemPath = $config['item_path'] ?? 'vacancy';
        $items    = $xml->xpath("//{$itemPath}") ?: [];

        return array_map(
            fn(SimpleXMLElement $item) => $this->normalise($item, $config),
            $items
        );
    }

    public function supports(string $type): bool
    {
        return $type === 'xml';
    }

    private function normalise(SimpleXMLElement $item, array $config): JobData
    {
        $map = $config['field_map'] ?? [];

        $get = fn(string $key, string $default) => (string) ($item->{$map[$key] ?? $default} ?? '');

        return JobData::fromArray([
            'external_id'             => $get('external_id', 'id') ?: uniqid(),
            'title'                   => $get('title', 'title'),
            'organisation'            => $get('organisation', 'employer'),
            'description'             => $get('description', 'description'),
            'location'                => $get('location', 'location'),
            'postcode'                => $get('postcode', 'postcode') ?: null,
            'salary_text'             => $get('salary_text', 'salary') ?: null,
            'band'                    => $get('band', 'pay_band') ?: null,
            'contract_type'           => $get('contract_type', 'contract_type') ?: null,
            'sponsorship_information' => $get('sponsorship', 'sponsorship') ?: null,
            'requirements'            => $get('requirements', 'requirements') ?: null,
            'closing_date'            => $get('closing_date', 'closing_date') ?: null,
            'published_date'          => $get('published_date', 'published_date') ?: null,
            'source_url'              => $get('source_url', 'url') ?: null,
            'raw_data'                => json_decode(json_encode($item), true) ?? [],
        ]);
    }
}
