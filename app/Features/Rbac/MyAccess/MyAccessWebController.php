<?php

namespace App\Features\Rbac\MyAccess;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MyAccessWebController
{
    /**
     * The roles and permissions of the authenticated user.
     */
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $roles = $user->roles->sortBy('name')->values();

        $permissionGroups = $user->getAllPermissions()
            ->pluck('name')
            ->sort()
            ->groupBy(fn (string $name) => Str::before($name, '.'));

        return view('features::Rbac.MyAccess.view', compact('user', 'roles', 'permissionGroups'));
    }
}
