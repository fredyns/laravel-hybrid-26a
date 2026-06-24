<?php

namespace App\DB;

use App\Enums\UserRole;
use App\Models\RBAC\Permission;
use Spatie\Permission\PermissionRegistrar;

/**
 * Base class for permission migrations.
 *
 * Simplifies adding permissions and assigning them to roles.
 * Supports multiple guards (web, sanctum).
 */
abstract class BasePermissionMigration extends BaseRbacMigration
{
    public function permissions(): array
    {
        return [
            // auto apply to all guards
            //  'permission_name',                  // only assigned to SUPER-ADMIN
            //  'permission_name' => ['user_role'], // assigned to SUPER-ADMIN and user_role
        ];
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->permissions() as $key => $value) {
            if (is_string($key) && is_array($value)) {
                $permission = $key;
                $roles = $value;
                $roles[] = UserRole::SUPER_ADMIN;
            } else {
                if (is_string($value)) {
                    $permission = $value;
                    $roles = [UserRole::SUPER_ADMIN];
                } else {
                    continue;
                }
            }

            $this->grant($permission, $roles);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * @param  string  $permission
     * @param  UserRole[]  $roles
     */
    protected function grant(string $permissionName, array $roleEnums): void
    {
        foreach ($this->guards as $guardName) {
            $roles = $this->getRoles($roleEnums, $guardName);
            $permission = Permission::findOrCreate($permissionName, $guardName);
            $permission->syncRoles($roles);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissionNames = [];
        foreach ($this->permissions() as $key => $value) {
            if (is_string($key) && is_array($value)) {
                $permissionNames[] = $key;
            } else {
                if (is_string($value)) {
                    $permissionNames = $value;
                }
            }
        }

        if (empty($permissionNames)) {
            return;
        }

        $permissions = Permission::whereIn('guard_name', $this->guards)
            ->whereIn('name', $permissionNames)
            ->get();

        foreach ($permissions as $permission) {
            $permission->syncRoles([]);
        }

        Permission::whereIn('guard_name', $this->guards)
            ->whereIn('name', $permissionNames)
            ->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
