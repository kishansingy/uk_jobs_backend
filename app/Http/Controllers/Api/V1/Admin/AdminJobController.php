<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobDetailResource;
use App\Http\Traits\ApiResponse;
use App\Models\Job;
use App\Services\AuditService;
use App\Services\Jobs\JobClassificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminJobController extends Controller
{
    use ApiResponse;

    public function __construct(
        private JobClassificationService $classifier,
        private AuditService $audit
    ) {}

    public function index(Request $request): JsonResponse
    {
        $jobs = Job::with(['source', 'profession', 'specialty'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('source_id'), fn($q) => $q->where('source_id', $request->source_id))
            ->when($request->filled('search'), fn($q) => $q->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('organisation', 'like', '%' . $request->search . '%');
            }))
            ->orderByDesc('created_at')
            ->paginate(25);

        return $this->success(
            JobDetailResource::collection($jobs),
            'Jobs retrieved',
            200,
            ['total' => $jobs->total()]
        );
    }

    public function update(Request $request, Job $job): JsonResponse
    {
        $request->validate([
            'status'        => ['sometimes', 'string'],
            'profession_id' => ['sometimes', 'nullable', 'exists:professions,id'],
            'specialty_id'  => ['sometimes', 'nullable', 'exists:specialties,id'],
            'band'          => ['sometimes', 'nullable', 'string', 'max:10'],
        ]);

        $job->update($request->validated());
        $this->audit->log('admin.job.updated', $request->user()->id, $job);

        return $this->success(new JobDetailResource($job));
    }

    public function reclassify(Request $request, Job $job): JsonResponse
    {
        $this->classifier->classify($job);
        $this->audit->log('admin.job.reclassified', $request->user()->id, $job);

        return $this->success(new JobDetailResource($job->fresh(['profession', 'specialty'])), 'Job reclassified');
    }

    public function destroy(Request $request, Job $job): JsonResponse
    {
        $job->update(['status' => 'removed']);
        $this->audit->log('admin.job.removed', $request->user()->id, $job);

        return $this->success(null, 'Job marked as removed');
    }
}
