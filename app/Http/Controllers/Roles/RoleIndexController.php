<?php

namespace App\Http\Controllers\Roles;

use App\Enums\AuthGuard;
use App\Http\Controllers\Controller;
use App\Models\RBAC\Role;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class RoleIndexController extends Controller
{
    use AuthorizesRequests;

    public function __invoke(Request $request)
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
}
