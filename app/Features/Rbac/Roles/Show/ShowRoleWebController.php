<?php

namespace App\Features\Rbac\Roles\Show;

use App\Models\RBAC\Role;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class ShowRoleWebController
{
    public function __invoke(Role $role): View
    {
        $permissionGroups = $role->permissions
            ->pluck('name')
            ->sort()
            ->groupBy(fn (string $name) => Str::before($name, '.'));

        return view('features::Rbac.Roles.Show.view', compact('role', 'permissionGroups'));
    }
}
