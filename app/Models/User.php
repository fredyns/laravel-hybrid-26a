<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\AuthGuard;
use App\Enums\UserRole;
use App\Models\RBAC\Role;
use App\Models\Traits\ModelDocBlocks;
use App\Models\Traits\Searchable;
use Carbon\Carbon;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    use HasRoles;
    use HasUuids;
    use ModelDocBlocks;
    use Searchable;

    protected array $searchableFields = [
        'name',
        'email',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * @param  string[]|UserRole[]  $roles
     */
    public function addRoleNames(array $roles): void
    {
        foreach ($roles as $role) {
            $this->addRoleName($role);
        }
    }

    public function addRoleName(UserRole|string $role): void
    {
        if ($role instanceof UserRole) {
            $role = $role->value;
        }

        $roleName = $role;

        foreach (AuthGuard::values() as $guardName) {
            $roleModel = Role::findOrCreate($roleName, $guardName);
            $this->addRole($roleModel);
        }
    }

    public function addRoleID(string $roleID): void
    {
        $role = Role::find($roleID);
        if ($role) {
            $this->addRole($role);
        }
    }

    public function addRole(Role $role): void
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $pivotRole = $columnNames['role_pivot_key'] ?? 'role_id';
        $modelKey = $columnNames['model_morph_key'] ?? 'model_id';

        $exists = DB::table($tableNames['model_has_roles'])
            ->where($pivotRole, $role->id)
            ->where('model_type', User::class)
            ->where($modelKey, $this->id)
            ->exists();

        if (! $exists) {
            DB::table($tableNames['model_has_roles'])->insert([
                $pivotRole => $role->id,
                'model_type' => User::class,
                $modelKey => $this->id,
            ]);
        }
    }

    /**
     * @param  UserRole[]|string[]  $roles
     */
    public function removeRoleNames(array $roles): void
    {
        foreach ($roles as $role) {
            $this->removeRoleName($role);
        }
    }

    public function removeRoleName(UserRole|string $role): void
    {
        if ($role instanceof UserRole) {
            $role = $role->value;
        }

        $roles = Role::where('name', $role)->get();
        if ($roles->count() > 0) {
            foreach ($roles as $role) {
                $this->removeRole($role);
            }
        }
    }

    public function removeRoleID(string $roleID): void
    {
        $role = Role::find($roleID);
        if ($role) {
            $this->removeRole($role);
        }
    }

    public function removeRole(Role $role): void
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $pivotRole = $columnNames['role_pivot_key'] ?? 'role_id';
        $modelKey = $columnNames['model_morph_key'] ?? 'model_id';

        DB::table($tableNames['model_has_roles'])
            ->where($pivotRole, $role->id)
            ->where('model_type', User::class)
            ->where($modelKey, $this->id)
            ->delete();
    }

    /**
     * A model may have multiple roles.
     */
    public function webRoles(): BelongsToMany
    {
        return $this->roles()->where('guard_name', 'web');
    }

    /**
     * A model may have multiple roles.
     */
    public function apiRoles(): BelongsToMany
    {
        return $this->roles()->where('guard_name', 'sanctum');
    }
}
