<?php

namespace App\Http\Controllers\Api\Roles;

use App\Http\Controllers\Controller;
use App\Models\RBAC\Role;
use Illuminate\Http\JsonResponse;

class DestroyController extends Controller
{
    public function __invoke(Role $role): JsonResponse
    {
        if ($role->isProtected()) {
            return response()->json([
                'message' => 'Protected roles cannot be deleted.',
            ], 422);
        }

        if ($role->users()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete a role that still has assigned users.',
            ], 422);
        }

        $role->delete();

        return response()->json([
            'message' => 'Role deleted',
        ]);
    }
}
