<?php

namespace App\Features\Rbac\Roles\Create;

use App\Features\Rbac\RoleResource;
use Illuminate\Http\JsonResponse;

class CreateRoleApiController
{
    public function __invoke(CreateRoleRequest $request, CreateRole $action): JsonResponse
    {
        /** @var array{name: string, guard: string, permissions?: list<string>} $validated */
        $validated = $request->validated();

        $role = $action->execute($validated);

        return response()->json([
            'message' => 'Role created',
            'role' => RoleResource::make($role),
        ], 201);
    }
}
