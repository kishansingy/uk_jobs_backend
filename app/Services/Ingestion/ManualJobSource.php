<?php

namespace App\Services\Ingestion;

use App\Contracts\JobSourceInterface;
use App\DTOs\JobData;
use App\Models\JobSource;

class ManualJobSource implements JobSourceInterface
{
    /**
     * Manual sources don't fetch — jobs are created directly via admin API.
     * This implementation exists to satisfy the interface for consistency.
     */
    public function fetch(JobSource $source): array
    {
        return [];
    }

    public function supports(string $type): bool
    {
        return $type === 'manual';
    }

    public function createFromArray(array $data): JobData
    {
        return JobData::fromArray($data);
    }
}
