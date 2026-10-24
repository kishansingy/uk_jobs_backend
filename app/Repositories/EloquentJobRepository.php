<?php

namespace App\Repositories;

use App\Models\Job;
use App\Repositories\Contracts\JobRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentJobRepository implements JobRepositoryInterface
{
    public function search(array $filters, string $sort = 'relevance', int $perPage = 20): LengthAwarePaginator
    {
        $query = Job::query()
            ->with(['profession', 'specialty', 'source'])
            ->visible(); // only active/non-expired

        // Full-text keyword search
        if (! empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->whereFullText(['title', 'description', 'organisation', 'location'], $keyword);
        }

        // Profession filter
        if (! empty($filters['profession_id'])) {
            $query->where('profession_id', $filters['profession_id']);
        }

        // Specialty filter
        if (! empty($filters['specialty_id'])) {
            $query->where('specialty_id', $filters['specialty_id']);
        }

        // Band filter
        if (! empty($filters['band'])) {
            $query->where('band', $filters['band']);
        }

        // Location text filter (until PostGIS/radius search is added)
        if (! empty($filters['location'])) {
            $query->where('location', 'like', '%' . $filters['location'] . '%');
        }

        // Salary range
        if (! empty($filters['salary_min'])) {
            $query->where('salary_max', '>=', $filters['salary_min']);
        }
        if (! empty($filters['salary_max'])) {
            $query->where('salary_min', '<=', $filters['salary_max']);
        }

        // Contract type
        if (! empty($filters['contract_type'])) {
            $query->where('contract_type', $filters['contract_type']);
        }

        // Sponsorship — only filter when explicitly requested
        if (isset($filters['sponsorship_available']) && $filters['sponsorship_available']) {
            $query->whereNotNull('sponsorship_information')
                ->where('sponsorship_information', '!=', '');
        }

        // Closing date — exclude already closed
        if (! empty($filters['closing_after'])) {
            $query->where('closing_date', '>=', $filters['closing_after']);
        }

        // Sorting
        $query = match($sort) {
            'newest'       => $query->orderByDesc('published_date'),
            'closing_soon' => $query->orderBy('closing_date'),
            'salary_high'  => $query->orderByDesc('salary_max'),
            'salary_low'   => $query->orderBy('salary_min'),
            default        => $query->orderByDesc('published_date'), // relevance falls back to newest
        };

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?Job
    {
        return Job::with(['profession', 'specialty', 'requirements', 'skills', 'source'])
            ->find($id);
    }
}
