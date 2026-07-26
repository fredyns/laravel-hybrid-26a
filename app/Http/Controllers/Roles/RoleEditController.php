<?php

namespace App\Http\Controllers\Roles;

use App\Http\Controllers\Controller;
use App\Models\RBAC\Role;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;

class RoleEditController extends Controller
{
    use AuthorizesRequests;

    public function __invoke(Role $role)
    {
        $this->authorize('rbac.update');

        $permissionGroups = $role->permissions
            ->pluck('name')
            ->sort()
            ->groupBy(fn (string $name) => Str::before($name, '.'));

        return view('roles.edit', compact('role', 'permissionGroups'));
    }
}
