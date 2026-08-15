<?php

namespace App\Features\Rbac\Roles\Show;

use App\Features\Rbac\RoleResource;
use App\Models\RBAC\Role;

class ShowRoleApiController
{
    public function __invoke(Role $role): RoleResource
    {
        $role->load('permissions:id,name')->loadCount(['permissions', 'users']);

        return RoleResource::make($role);
    }
}
