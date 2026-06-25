<?php

use App\DB\BasePermissionMigration;
use App\Enums\UserRole;

return new class extends BasePermissionMigration
{
    public function permissions(): array
    {
        return [
            // auto apply to all guards
            //  'permission_name',                  // only assigned to SUPER-ADMIN
            //  'permission_name' => ['user_role'], // assigned to SUPER-ADMIN and user_role
            'users.index' => [UserRole::EXTERNAL],
            'users.show',
            'users.create',
            'users.update',
            'users.delete',
            'users.change-password',
        ];
    }
};
