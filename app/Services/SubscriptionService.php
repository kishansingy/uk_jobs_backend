<?php

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use App\Enums\SubscriptionStatus;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;

class SubscriptionService
{
    public function __construct(private PaymentGatewayInterface $gateway) {}

    public function subscribe(User $user, Plan $plan, array $paymentData): Subscription
    {
        $result = $this->gateway->createSubscription($user, $plan, $paymentData);

        return Subscription::create([
            'user_id'                  => $user->id,
            'plan_id'                  => $plan->id,
            'status'                   => SubscriptionStatus::Active->value,
            'billing_period'           => $paymentData['billing_period'] ?? 'monthly',
            'provider'                 => $this->gateway->getName(),
            'provider_subscription_id' => $result['provider_subscription_id'],
            'starts_at'                => now(),
            'ends_at'                  => null,
        ]);
    }

    public function cancel(Subscription $subscription): void
    {
        $this->gateway->cancelSubscription($subscription->provider_subscription_id);

        $subscription->update([
            'status'       => SubscriptionStatus::Cancelled->value,
            'cancelled_at' => now(),
        ]);
    }

    public function currentPlan(User $user): ?Subscription
    {
        return $user->subscriptions()
            ->with(['plan.features'])
            ->where('status', SubscriptionStatus::Active->value)
            ->latest('starts_at')
            ->first();
    }

    public function userHasFeature(User $user, string $featureKey): bool
    {
        $sub = $this->currentPlan($user);
        return $sub?->plan?->hasFeature($featureKey) ?? false;
    }
}
