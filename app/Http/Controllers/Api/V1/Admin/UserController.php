<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponse;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponse;

    public function __construct(private AuditService $audit) {}

    public function index(Request $request): JsonResponse
    {
        $users = User::with('roles')
            ->when($request->has('search'), fn($q) =>
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
            )
            ->when($request->has('role'), fn($q) =>
                $q->role($request->role)
            )
            ->orderByDesc('created_at')
            ->paginate(25);

        return $this->success(
            UserResource::collection($users),
            'Users retrieved',
            200,
            ['total' => $users->total()]
        );
    }

    public function show(User $user): JsonResponse
    {
        return $this->success(new UserResource(
            $user->load('candidateProfile.profession', 'roles', 'activeSubscription.plan')
        ));
    }

    public function toggleActive(Request $request, User $user): JsonResponse
    {
        $user->update(['active' => ! $user->active]);
        $this->audit->log('admin.user.toggled_active', $request->user()->id, $user,
            ['active' => ! $user->active], ['active' => $user->active]);

        return $this->success(['active' => $user->active], 'User status updated');
    }

    public function assignRole(Request $request, User $user): JsonResponse
    {
        $request->validate(['role' => ['required', 'string', 'exists:roles,name']]);

        $user->syncRoles([$request->role]);
        $this->audit->log('admin.user.role_assigned', $request->user()->id, $user,
            [], ['role' => $request->role]);

        return $this->success(null, 'Role assigned');
    }
}
