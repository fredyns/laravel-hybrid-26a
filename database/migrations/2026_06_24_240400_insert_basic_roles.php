<?php

use App\Enums\UserRole;
use App\Models\RBAC\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (UserRole::values() as $role) {
            app(Role::class)->findOrCreate($role, 'web');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Role::whereIn('name', UserRole::values())->whereIn('guard_name', ['web'])->delete();
    }
};
