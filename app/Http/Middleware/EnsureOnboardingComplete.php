<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboardingComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && ! $request->user()->onboarding_completed) {
            return response()->json([
                'success'  => false,
                'message'  => 'Please complete your profile setup before continuing.',
                'redirect' => '/onboarding',
            ], 403);
        }

        return $next($request);
    }
}
