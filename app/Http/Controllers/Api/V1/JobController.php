<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Job\JobSearchRequest;
use App\Http\Resources\JobDetailResource;
use App\Http\Resources\JobResource;
use App\Http\Traits\ApiResponse;
use App\Models\SavedJob;
use App\Repositories\Contracts\JobRepositoryInterface;
use App\Services\Jobs\JobMatchingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobController extends Controller
{
    use ApiResponse;

    public function __construct(
        private JobRepositoryInterface $jobs,
        private JobMatchingService $matching
    ) {}

    public function index(JobSearchRequest $request): JsonResponse
    {
        $paginator = $this->jobs->search(
            $request->validated(),
            $request->input('sort', 'newest'),
            $request->integer('per_page', 20)
        );

        return $this->success(
            JobResource::collection($paginator),
            'Jobs retrieved',
            200,
            [
                'total'        => $paginator->total(),
                'per_page'     => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
            ]
        );
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $job = $this->jobs->findById($id);

        if (! $job) {
            return $this->error('Job not found', 404);
        }

        // Compute match score if user is authenticated
        if ($request->user()) {
            $this->matching->match($request->user(), $job);
            $job->load('matches');
        }

        return $this->success(new JobDetailResource($job));
    }

    public function save(Request $request, int $id): JsonResponse
    {
        $job = $this->jobs->findById($id);
        if (! $job) {
            return $this->error('Job not found', 404);
        }

        SavedJob::firstOrCreate([
            'user_id' => $request->user()->id,
            'job_id'  => $id,
        ]);

        return $this->success(null, 'Job saved');
    }

    public function unsave(Request $request, int $id): JsonResponse
    {
        SavedJob::where('user_id', $request->user()->id)
            ->where('job_id', $id)
            ->delete();

        return $this->success(null, 'Job removed from saved');
    }

    public function match(Request $request, int $id): JsonResponse
    {
        $job = $this->jobs->findById($id);
        if (! $job) {
            return $this->error('Job not found', 404);
        }

        $result = $this->matching->calculate($request->user(), $job);

        return $this->success([
            'score'       => $result->score,
            'factors'     => $result->factors,
            'explanation' => $result->explanation,
            'gaps'        => $result->gaps,
            'disclaimer'  => 'Match scores are for guidance only. They are not official NHS assessments.',
        ]);
    }
}
