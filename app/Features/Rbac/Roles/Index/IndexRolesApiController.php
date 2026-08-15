<?php

namespace App\Features\Rbac\Roles\Index;

use App\Enums\AuthGuard;
use App\Features\Rbac\RoleResource;
use App\Models\RBAC\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class IndexRolesApiController
{
    /**
     * List roles with optional search, guard filter and pagination.
     */
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'guard' => ['nullable', 'string', Rule::in(AuthGuard::values())],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $roles = Role::query()
            ->withCount(['permissions', 'users'])
            ->when(($validated['search'] ?? '') !== '', fn ($query) => $query->search($validated['search']))
            ->when(($validated['guard'] ?? '') !== '', fn ($query) => $query->where('guard_name', $validated['guard']))
            ->orderByDesc('created_at')
            ->paginate($validated['per_page'] ?? 10);

        return RoleResource::collection($roles);
    }
}
