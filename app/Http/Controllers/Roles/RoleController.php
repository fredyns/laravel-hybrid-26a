<?php

namespace App\Http\Controllers\Roles;

use App\Enums\AuthGuard;
use App\Http\Controllers\Controller;
use App\Models\RBAC\Role;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('rbac.index');

        $search = $request->query('q', '');
        $guard = $request->query('guard', '');
        $perPage = (int) $request->query('per_page', 10);

        $roles = Role::query()
            ->withCount(['permissions', 'users'])
            ->when($search !== '', fn ($query) => $query->search($search))
            ->when($guard !== '', fn ($query) => $query->where('guard_name', $guard))
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->appends($request->query());

        $guards = AuthGuard::values();

        return view('roles.index', compact(
            'roles',
            'search',
            'guard',
            'perPage',
            'guards'
        ));
    }

    public function show(Role $role)
    {
        $this->authorize('rbac.show');

        $permissionGroups = $role->permissions
            ->pluck('name')
            ->sort()
            ->groupBy(fn (string $name) => Str::before($name, '.'));

        return view('roles.show', compact('role', 'permissionGroups'));
    }
}
