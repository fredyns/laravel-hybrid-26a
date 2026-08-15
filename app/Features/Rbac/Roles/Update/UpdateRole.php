<?php

namespace App\Features\Rbac\Roles\Update;

use App\Models\RBAC\Role;

class UpdateRole
{
    /**
     * Protected roles keep their name and guard locked; permissions always sync.
     *
     * @param  array{name: string, guard: string, permissions?: list<string>}  $input
     */
    public function execute(Role $role, array $input): Role
    {
        if (! $role->isProtected()) {
            $role->name = $input['name'];
            $role->guard_name = $input['guard'];
            $role->save();
        }

        $role->syncPermissions($input['permissions'] ?? []);

        return $role->load('permissions:id,name')->loadCount(['permissions', 'users']);
    }
}
