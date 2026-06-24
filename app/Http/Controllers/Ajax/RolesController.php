<?php

namespace App\Http\Controllers\Ajax;

use App\Models\RBAC\Role;
use Illuminate\Http\JsonResponse;

class RolesController
{
    /**
     * Get all available web roles.
     */
    public function __invoke(): JsonResponse
    {
        $roles = Role::query()
            ->select(['id', 'name'])
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $roles,
        ]);
    }
}
