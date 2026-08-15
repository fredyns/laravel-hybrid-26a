<?php

namespace App\Features\Rbac\Permissions\Index;

use App\Enums\AuthGuard;
use App\Features\Rbac\GroupedPermissions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IndexPermissionsApiController
{
    /**
     * Permissions for a guard, grouped by resource prefix and ordered by the
     * conventional CRUD action sequence — mirrors the role form.
     */
    public function __invoke(Request $request, GroupedPermissions $permissions): JsonResponse
    {
        $validated = $request->validate([
            'guard' => ['required', 'string', Rule::in(AuthGuard::values())],
        ]);

        return response()->json([
            'data' => $permissions->forGuard($validated['guard']),
        ]);
    }
}
