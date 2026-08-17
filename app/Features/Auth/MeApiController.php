<?php

namespace App\Features\Auth;

use App\Features\User\UserResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeApiController extends Controller
{
    /**
     * The authenticated user together with their API-guard roles and
     * permissions, so clients can drive RBAC-aware interfaces.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        $roles = $user->apiRoles()->with('permissions:id,name')->get();

        return response()->json([
            ...UserResource::make($user)->resolve(),
            'roles' => $roles->pluck('name')->sort()->values(),
            'permissions' => $roles
                ->flatMap(fn ($role) => $role->permissions->pluck('name'))
                ->unique()
                ->sort()
                ->values(),
        ]);
    }
}
