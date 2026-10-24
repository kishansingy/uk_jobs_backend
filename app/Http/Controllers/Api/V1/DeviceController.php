<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\UserDevice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    use ApiResponse;

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'device_token' => ['required', 'string', 'max:500'],
            'platform'     => ['required', 'in:ios,android,web'],
            'app_version'  => ['nullable', 'string', 'max:20'],
        ]);

        UserDevice::updateOrCreate(
            ['device_token' => $request->device_token],
            [
                'user_id'      => $request->user()->id,
                'platform'     => $request->platform,
                'app_version'  => $request->app_version,
                'last_seen_at' => now(),
                'active'       => true,
            ]
        );

        return $this->success(null, 'Device registered');
    }

    public function revoke(Request $request, string $token): JsonResponse
    {
        UserDevice::where('user_id', $request->user()->id)
            ->where('device_token', $token)
            ->update(['active' => false]);

        return $this->success(null, 'Device unregistered');
    }
}
