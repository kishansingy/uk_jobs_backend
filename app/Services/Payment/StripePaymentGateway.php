<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class StripePaymentGateway implements PaymentGatewayInterface
{
    private string $secret;

    public function __construct()
    {
        $this->secret = config('payment.stripe.secret');
    }

    public function createSubscription(User $user, Plan $plan, array $paymentData): array
    {
        $billing  = $paymentData['billing_period'] ?? 'monthly';
        $priceId  = $billing === 'annual' ? $plan->provider_annual_id : $plan->provider_monthly_id;

        if (empty($priceId)) {
            throw new RuntimeException("No Stripe price ID configured for plan [{$plan->slug}] / {$billing}.");
        }

        // Create or retrieve Stripe customer
        $customerId = $this->ensureStripeCustomer($user);

        $response = $this->stripe('POST', '/v1/subscriptions', [
            'customer'         => $customerId,
            'items'            => [['price' => $priceId]],
            'payment_behavior' => 'default_incomplete',
            'expand'           => ['latest_invoice.payment_intent'],
        ]);

        return [
            'provider_subscription_id' => $response['id'],
            'client_secret'            => $response['latest_invoice']['payment_intent']['client_secret'] ?? null,
            'status'                   => $response['status'],
        ];
    }

    public function cancelSubscription(string $providerReference): bool
    {
        $response = $this->stripe('DELETE', "/v1/subscriptions/{$providerReference}");
        return ($response['status'] ?? '') === 'canceled';
    }

    public function handleWebhook(array $payload, string $signature): void
    {
        // Verify webhook signature then dispatch events based on payload type.
        // Actual verification requires stripe/stripe-php or manual HMAC check.
        $webhookSecret = config('payment.stripe.webhook_secret');

        $computedSig = hash_hmac('sha256', json_encode($payload), $webhookSecret);
        if (! hash_equals($computedSig, $signature)) {
            throw new RuntimeException('Invalid Stripe webhook signature.');
        }
    }

    public function getName(): string
    {
        return 'stripe';
    }

    private function ensureStripeCustomer(User $user): string
    {
        $response = $this->stripe('POST', '/v1/customers', [
            'email' => $user->email,
            'name'  => $user->name,
            'metadata' => ['user_id' => $user->id],
        ]);

        return $response['id'];
    }

    private function stripe(string $method, string $path, array $data = []): array
    {
        $response = Http::withBasicAuth($this->secret, '')
            ->timeout(30)
            ->{strtolower($method)}('https://api.stripe.com' . $path, $data);

        if ($response->failed()) {
            throw new RuntimeException('Stripe API error: ' . $response->body());
        }

        return $response->json();
    }
}
