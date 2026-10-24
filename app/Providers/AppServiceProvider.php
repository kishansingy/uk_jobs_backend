<?php

namespace App\Providers;

use App\Contracts\AiProviderInterface;
use App\Contracts\PaymentGatewayInterface;
use App\Services\AI\GeminiProvider;
use App\Services\AI\OpenAiProvider;
use App\Services\Ingestion\ApiJobSource;
use App\Services\Ingestion\ManualJobSource;
use App\Services\Ingestion\NhsJobsSource;
use App\Services\Ingestion\XmlJobSource;
use App\Services\Ingestion\JobIngestionService;
use App\Services\Payment\StripePaymentGateway;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // AI Provider — swappable via AI_PROVIDER env
        $this->app->singleton(AiProviderInterface::class, function () {
            return match(config('ai.provider')) {
                'openai' => new OpenAiProvider(),
                default  => new GeminiProvider(),
            };
        });

        // Payment Gateway — swappable via PAYMENT_PROVIDER env
        $this->app->singleton(PaymentGatewayInterface::class, function () {
            return match(config('payment.provider')) {
                default => new StripePaymentGateway(),
            };
        });

        // Job Ingestion — register all source handlers (Open/Closed principle: add new handlers without modifying this line)
        $this->app->singleton(JobIngestionService::class, function () {
            return new JobIngestionService([
                new NhsJobsSource(),
                new ApiJobSource(),
                new XmlJobSource(),
                new ManualJobSource(),
            ]);
        });
    }

    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
