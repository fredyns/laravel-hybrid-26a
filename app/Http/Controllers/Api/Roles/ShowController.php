<?php

namespace App\Http\Controllers\Api\Roles;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Models\RBAC\Role;

class ShowController extends Controller
{
    public function __invoke(Role $role): RoleResource
    {
        $role->load('permissions:id,name')->loadCount(['permissions', 'users']);

        return RoleResource::make($role);
    }
}
