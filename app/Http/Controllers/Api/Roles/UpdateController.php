<?php

namespace App\Http\Controllers\Api\Roles;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Roles\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\RBAC\Role;
use Illuminate\Http\JsonResponse;

class UpdateController extends Controller
{
    public function __invoke(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        $permissions = $request->validated('permissions') ?? [];

        // The super-admin role must always retain at least one permission.
        if ($role->name === UserRole::SUPER_ADMIN->value && $permissions === []) {
            return response()->json([
                'message' => 'The super-admin role must keep at least one permission.',
                'errors' => ['permissions' => ['The super-admin role must keep at least one permission.']],
            ], 422);
        }

        // Protected roles keep their name and guard locked.
        if (! $role->isProtected()) {
            $role->name = $request->validated('name');
            $role->guard_name = $request->validated('guard');
            $role->save();
        }

        $role->syncPermissions($permissions);

        $role->load('permissions:id,name')->loadCount(['permissions', 'users']);

        return response()->json([
            'message' => 'Role updated',
            'role' => RoleResource::make($role),
        ]);
    }
}
