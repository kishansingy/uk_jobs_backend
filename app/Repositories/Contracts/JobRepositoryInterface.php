<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface JobRepositoryInterface
{
    public function search(array $filters, string $sort = 'relevance', int $perPage = 20): LengthAwarePaginator;

    public function findById(int $id): ?\App\Models\Job;
}
