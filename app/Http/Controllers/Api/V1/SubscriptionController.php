<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\StoreSubscriptionRequest;
use App\Http\Resources\SubscriptionResource;
use App\Http\Traits\ApiResponse;
use App\Models\Plan;
use App\Services\AuditService;
use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    use ApiResponse;

    public function __construct(
        private SubscriptionService $service,
        private AuditService $audit
    ) {}

    public function plans(): JsonResponse
    {
        $plans = Plan::where('active', true)
            ->with('features')
            ->orderBy('sort_order')
            ->get()
            ->map(fn($p) => [
                'id'            => $p->id,
                'name'          => $p->name,
                'slug'          => $p->slug,
                'description'   => $p->description,
                'price_monthly' => $p->price_monthly,
                'price_annual'  => $p->price_annual,
                'currency'      => $p->currency,
                'features'      => $p->features->map(fn($f) => [
                    'key'   => $f->feature_key,
                    'value' => $f->feature_value,
                ]),
            ]);

        return $this->success($plans);
    }

    public function current(Request $request): JsonResponse
    {
        $sub = $this->service->currentPlan($request->user());

        if (! $sub) {
            return $this->success(null, 'No active subscription');
        }

        return $this->success(new SubscriptionResource($sub));
    }

    public function subscribe(StoreSubscriptionRequest $request): JsonResponse
    {
        $plan = Plan::findOrFail($request->plan_id);
        $sub  = $this->service->subscribe($request->user(), $plan, $request->validated());

        $this->audit->log('subscription.created', $request->user()->id, $sub);

        return $this->created(new SubscriptionResource($sub->load('plan.features')));
    }

    public function cancel(Request $request): JsonResponse
    {
        $sub = $this->service->currentPlan($request->user());

        if (! $sub) {
            return $this->error('No active subscription to cancel.', 422);
        }

        $this->service->cancel($sub);
        $this->audit->log('subscription.cancelled', $request->user()->id, $sub);

        return $this->success(null, 'Subscription cancelled');
    }
}
