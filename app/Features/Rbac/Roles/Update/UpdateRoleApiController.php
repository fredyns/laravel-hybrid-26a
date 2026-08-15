<?php

namespace App\Features\Rbac\Roles\Update;

use App\Features\Rbac\RoleResource;
use App\Models\RBAC\Role;
use Illuminate\Http\JsonResponse;

class UpdateRoleApiController
{
    public function __invoke(UpdateRoleRequest $request, Role $role, UpdateRole $action): JsonResponse
    {
        /** @var array{name: string, guard: string, permissions?: list<string>} $validated */
        $validated = $request->validated();

        $role = $action->execute($role, $validated);

        return response()->json([
            'message' => 'Role updated',
            'role' => RoleResource::make($role),
        ]);
    }
}
