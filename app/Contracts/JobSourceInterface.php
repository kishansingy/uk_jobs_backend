<?php

namespace App\Contracts;

use App\DTOs\JobData;
use App\Models\JobSource;

interface JobSourceInterface
{
    /**
     * Fetch jobs from this source and return normalised DTOs.
     *
     * @return JobData[]
     */
    public function fetch(JobSource $source): array;

    /**
     * Whether this source type is supported by this implementation.
     */
    public function supports(string $type): bool;
}
