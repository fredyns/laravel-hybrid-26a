<?php

use App\Enums\UserRole;

return new class extends \App\DB\BaseAssignUserRoleMigration {
    public function assignments(): array
    {
        return [
            //  'user_email' => ['user_role'],
            'dm@fredyns.id' => [UserRole::SUPER_ADMIN],
            'fredy.ns@bki.co.id' => [UserRole::SUPER_ADMIN],
            'ict@bki.co.id' => [UserRole::INTERNAL],
            'fredy.ns@gmail.com' => [UserRole::EXTERNAL],
            'admin@admin.com' => [UserRole::SUPER_ADMIN],
            'INTERNAL@INTERNAL.com' => [UserRole::INTERNAL],
            'user@user.com' => [UserRole::EXTERNAL],
        ];
    }
};
