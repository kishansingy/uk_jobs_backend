<?php

use App\Http\Middleware\EnsureActiveSubscription;
use App\Http\Middleware\EnsureAdminRole;
use App\Http\Middleware\EnsureOnboardingComplete;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Return null so unauthenticated API requests throw AuthenticationException (caught as 401 JSON below)
        $middleware->redirectGuestsTo(fn(Request $request) => null);

        // Sanctum stateful domains for SPA cookie auth
        $middleware->statefulApi();

        // Alias middleware for use in route definitions
        $middleware->alias([
            'admin'        => EnsureAdminRole::class,
            'onboarded'    => EnsureOnboardingComplete::class,
            'subscribed'   => EnsureActiveSubscription::class,
        ]);

        // API rate limiting
        $middleware->throttleApi();

        // Append security headers to all responses
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Return JSON for all API exceptions
        $exceptions->shouldRenderJsonWhen(
            fn(Request $request, \Throwable $e) => $request->is('api/*')
        );

        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $e->errors(),
                ], 422);
            }
        });

        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }
        });

        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden.',
                ], 403);
            }
        });
    })->create();
