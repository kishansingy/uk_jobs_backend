<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponse;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(private AuditService $audit) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'country'  => $request->country,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('free-user');

        $token = $user->createToken('api')->plainTextToken;

        $this->audit->log('user.registered', $user->id, $user);

        return $this->created([
            'user'  => new UserResource($user),
            'token' => $token,
        ], 'Registration successful. Please verify your email.');
    }

    public function login(LoginRequest $request): JsonResponse
    {
        if (! Auth::attempt($request->only('email', 'password'))) {
            $this->audit->log('auth.login_failed', null);
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = Auth::user();

        if (! $user->active) {
            Auth::logout();
            return $this->error('Your account has been deactivated.', 403);
        }

        $token = $user->createToken('api')->plainTextToken;

        $this->audit->log('auth.login', $user->id, $user);

        return $this->success([
            'user'  => new UserResource($user),
            'token' => $token,
        ], 'Login successful');
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('candidateProfile.profession', 'candidateProfile.specialty');
        return $this->success(new UserResource($user));
    }

    public function logout(Request $request): JsonResponse
    {
        $this->audit->log('auth.logout', $request->user()->id);
        $request->user()->currentAccessToken()->delete();
        return $this->success(null, 'Logged out successfully');
    }
}
