<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SupportingStatementResource;
use App\Http\Traits\ApiResponse;
use App\Models\Job;
use App\Models\SupportingStatement;
use App\Services\AI\SupportingStatementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupportingStatementController extends Controller
{
    use ApiResponse;

    public function __construct(private SupportingStatementService $service) {}

    public function index(Request $request): JsonResponse
    {
        $statements = SupportingStatement::where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get();

        return $this->success(SupportingStatementResource::collection($statements));
    }

    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'job_id'     => ['required', 'exists:jobs,id'],
            'experience' => ['nullable', 'string', 'max:3000'],
            'skills'     => ['nullable', 'string', 'max:1000'],
        ]);

        $job = Job::findOrFail($request->job_id);

        $statement = $this->service->generate(
            $request->user(),
            $job,
            $request->only('experience', 'skills')
        );

        return $this->created(new SupportingStatementResource($statement));
    }

    public function show(Request $request, SupportingStatement $supportingStatement): JsonResponse
    {
        $this->authorize('view', $supportingStatement);
        return $this->success(new SupportingStatementResource($supportingStatement));
    }

    public function update(Request $request, SupportingStatement $supportingStatement): JsonResponse
    {
        $this->authorize('update', $supportingStatement);

        $request->validate([
            'content' => ['required', 'string'],
            'title'   => ['nullable', 'string', 'max:255'],
        ]);

        $supportingStatement->update($request->only('content', 'title'));

        return $this->success(new SupportingStatementResource($supportingStatement), 'Statement updated');
    }

    public function destroy(Request $request, SupportingStatement $supportingStatement): JsonResponse
    {
        $this->authorize('delete', $supportingStatement);
        $supportingStatement->delete();
        return $this->noContent();
    }
}
