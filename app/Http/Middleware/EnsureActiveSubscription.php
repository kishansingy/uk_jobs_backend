<?php

namespace App\Http\Middleware;

use App\Services\SubscriptionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveSubscription
{
    public function __construct(private SubscriptionService $subscriptions) {}

    public function handle(Request $request, Closure $next, string $feature = ''): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if ($feature && ! $this->subscriptions->userHasFeature($user, $feature)) {
            return response()->json([
                'success' => false,
                'message' => 'This feature requires a Pro or Premium subscription.',
                'upgrade_url' => '/subscription/plans',
            ], 402);
        }

        if (! $feature && ! $this->subscriptions->currentPlan($user)) {
            return response()->json([
                'success' => false,
                'message' => 'An active subscription is required.',
                'upgrade_url' => '/subscription/plans',
            ], 402);
        }

        return $next($request);
    }
}
