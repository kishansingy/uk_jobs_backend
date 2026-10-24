<?php

namespace App\Contracts;

use App\Models\Plan;
use App\Models\User;

interface PaymentGatewayInterface
{
    public function createSubscription(User $user, Plan $plan, array $paymentData): array;

    public function cancelSubscription(string $providerReference): bool;

    public function handleWebhook(array $payload, string $signature): void;

    public function getName(): string;
}
