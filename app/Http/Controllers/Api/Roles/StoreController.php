<?php

namespace App\Http\Controllers\Api\Roles;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Roles\StoreRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\RBAC\Role;
use Illuminate\Http\JsonResponse;

class StoreController extends Controller
{
    public function __invoke(StoreRoleRequest $request): JsonResponse
    {
        $role = Role::create([
            'name' => $request->validated('name'),
            'guard_name' => $request->validated('guard'),
        ]);

        $permissions = $request->validated('permissions') ?? [];
        if ($permissions !== []) {
            $role->syncPermissions($permissions);
        }

        $role->load('permissions:id,name')->loadCount(['permissions', 'users']);

        return response()->json([
            'message' => 'Role created',
            'role' => RoleResource::make($role),
        ], 201);
    }
}
