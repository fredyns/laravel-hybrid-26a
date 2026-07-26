<?php

namespace App\Http\Controllers\Roles;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RoleCreateController extends Controller
{
    use AuthorizesRequests;

    public function __invoke()
    {
        $this->authorize('rbac.create');

        return view('roles.create');
    }
}
