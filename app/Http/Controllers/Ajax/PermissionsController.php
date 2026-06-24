<?php

namespace App\Http\Controllers\Ajax;

use App\Models\RBAC\Permission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PermissionsController
{
    /**
     * Get all available permissions grouped by resource.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $guardName = $request->input('guard_name', 'web');

        $permissions = Permission::query()
            ->select(['id', 'name'])
            ->where('guard_name', $guardName)
            ->orderBy('name')
            ->get()
            ->groupBy(fn (Permission $permission) => explode('.', $permission->name)[0])
            ->toArray();

        return response()->json([
            'data' => $permissions,
        ]);
    }
}
