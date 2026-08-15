<?php

namespace App\Features\Rbac\Roles\Create;

use App\Models\RBAC\Role;

class CreateRole
{
    /**
     * @param  array{name: string, guard: string, permissions?: list<string>}  $input
     */
    public function execute(array $input): Role
    {
        $role = Role::query()->create([
            'name' => $input['name'],
            'guard_name' => $input['guard'],
        ]);

        $permissions = $input['permissions'] ?? [];

        if ($permissions !== []) {
            $role->syncPermissions($permissions);
        }

        return $role->load('permissions:id,name')->loadCount(['permissions', 'users']);
    }
}
