<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        // API requests should get a 401 JSON response, not a redirect
        return $request->expectsJson() ? null : null;
    }
}
