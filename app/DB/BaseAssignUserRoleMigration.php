<?php

namespace App\DB;

use App\Models\User;

/**
 * Base class for permission migrations.
 *
 * Simplifies adding permissions and assigning them to roles.
 * Supports multiple guards (web, sanctum).
 *
 */
abstract class BaseAssignUserRoleMigration extends BaseRbacMigration
{
    /**
     * lists of assignments
     * extend this method to add your own assignments
     *
     * @return array
     */
    public function assignments(): array
    {
        return [
            //  'user_email' => [\App\Enums\UserRole::___],
        ];
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->assignments() as $userEmail => $roleEnums) {
            $user = User::where('email', $userEmail)->first();
            $user?->addRoleNames($roleEnums);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->assignments() as $userEmail => $roleEnums) {
            $user = User::where('email', $userEmail)->first();
            $user?->removeRoleNames($roleEnums);
        }
    }
}
