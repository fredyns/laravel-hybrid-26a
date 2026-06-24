<?php

namespace App\DB;

use App\Models\RBAC\Role;
use Illuminate\Database\Migrations\Migration;

/**
 * Base class for permission migrations.
 *
 * Simplifies adding permissions and assigning them to roles.
 * Supports multiple guards (web, sanctum).
 */
abstract class BaseRbacMigration extends Migration
{
    protected array $guards = ['web', 'sanctum'];

    /**
     * @return Role[]
     */
    protected function getRoles(array $roleEnums, string $guardName): array
    {
        $roleNames = array_map(fn ($roleEnum) => $roleEnum->value, $roleEnums);
        $roleNames = array_unique($roleNames);
        $roles = [];
        foreach ($roleNames as $roleName) {
            $roles[] = Role::findOrCreate($roleName, $guardName);
        }

        return $roles;
    }
}
