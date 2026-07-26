<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MyAccessController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $roles = $user->roles->sortBy('name')->values();

        $permissionGroups = $user->getAllPermissions()
            ->pluck('name')
            ->sort()
            ->groupBy(fn (string $name) => Str::before($name, '.'));

        return view('my-access', compact('user', 'roles', 'permissionGroups'));
    }
}
