<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Alert\StoreAlertRequest;
use App\Http\Resources\AlertResource;
use App\Http\Traits\ApiResponse;
use App\Models\JobAlert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $alerts = JobAlert::where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get();

        return $this->success(AlertResource::collection($alerts));
    }

    public function store(StoreAlertRequest $request): JsonResponse
    {
        $alert = JobAlert::create(array_merge(
            $request->validated(),
            ['user_id' => $request->user()->id]
        ));

        return $this->created(new AlertResource($alert));
    }

    public function show(Request $request, JobAlert $alert): JsonResponse
    {
        $this->authorize('view', $alert);
        return $this->success(new AlertResource($alert));
    }

    public function update(StoreAlertRequest $request, JobAlert $alert): JsonResponse
    {
        $this->authorize('update', $alert);
        $alert->update($request->validated());
        return $this->success(new AlertResource($alert), 'Alert updated');
    }

    public function destroy(Request $request, JobAlert $alert): JsonResponse
    {
        $this->authorize('delete', $alert);
        $alert->delete();
        return $this->noContent();
    }
}
