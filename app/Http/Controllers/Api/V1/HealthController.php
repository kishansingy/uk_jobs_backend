<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $checks = [
            'application' => true,
            'database'    => $this->checkDatabase(),
            'redis'       => $this->checkRedis(),
            'queue'       => $this->checkQueue(),
        ];

        $healthy = ! in_array(false, $checks, true);

        return response()->json([
            'success' => $healthy,
            'status'  => $healthy ? 'healthy' : 'degraded',
            'checks'  => $checks,
            'time'    => now()->toISOString(),
        ], $healthy ? 200 : 503);
    }

    private function checkDatabase(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    private function checkRedis(): bool
    {
        try {
            Cache::store('redis')->put('health_check', 1, 10);
            return Cache::store('redis')->get('health_check') === 1;
        } catch (\Throwable) {
            return false;
        }
    }

    private function checkQueue(): bool
    {
        try {
            // Check that the queue connection is responsive
            Queue::size('default');
            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}
