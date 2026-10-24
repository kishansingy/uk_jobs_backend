<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\StoreApplicationRequest;
use App\Http\Requests\Application\UpdateApplicationRequest;
use App\Http\Resources\ApplicationResource;
use App\Http\Traits\ApiResponse;
use App\Models\Application;
use App\Models\ApplicationStatusHistory;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    use ApiResponse;

    public function __construct(private AuditService $audit) {}

    public function index(Request $request): JsonResponse
    {
        $apps = Application::with(['job.profession', 'statusHistory'])
            ->where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get();

        return $this->success(ApplicationResource::collection($apps));
    }

    public function store(StoreApplicationRequest $request): JsonResponse
    {
        $existing = Application::where('user_id', $request->user()->id)
            ->where('job_id', $request->job_id)
            ->first();

        if ($existing) {
            return $this->error('You already have an application for this job.', 422);
        }

        $app = Application::create([
            'user_id' => $request->user()->id,
            'job_id'  => $request->job_id,
            'status'  => $request->status,
            'notes'   => $request->notes,
        ]);

        ApplicationStatusHistory::create([
            'application_id' => $app->id,
            'status'         => $app->status->value,
            'changed_by'     => $request->user()->id,
            'changed_at'     => now(),
        ]);

        return $this->created(new ApplicationResource($app->load('job')));
    }

    public function show(Request $request, Application $application): JsonResponse
    {
        $this->authorize('view', $application);
        return $this->success(new ApplicationResource(
            $application->load('job.profession', 'statusHistory')
        ));
    }

    public function update(UpdateApplicationRequest $request, Application $application): JsonResponse
    {
        $this->authorize('update', $application);

        $oldStatus = $application->status->value;
        $application->update($request->validated());

        if ($request->has('status') && $request->status !== $oldStatus) {
            ApplicationStatusHistory::create([
                'application_id' => $application->id,
                'status'         => $request->status,
                'notes'          => $request->notes,
                'changed_by'     => $request->user()->id,
                'changed_at'     => now(),
            ]);

            // Mark applied_at when transitioning to applied
            if ($request->status === 'applied' && ! $application->applied_at) {
                $application->update(['applied_at' => now()]);
            }
        }

        $this->audit->log('application.updated', $request->user()->id, $application);

        return $this->success(new ApplicationResource($application->load('statusHistory')));
    }

    public function destroy(Request $request, Application $application): JsonResponse
    {
        $this->authorize('delete', $application);
        // Soft status rather than hard delete — preserve history
        $application->update(['status' => 'withdrawn']);
        ApplicationStatusHistory::create([
            'application_id' => $application->id,
            'status'         => 'withdrawn',
            'changed_by'     => $request->user()->id,
            'changed_at'     => now(),
        ]);
        return $this->noContent();
    }
}
