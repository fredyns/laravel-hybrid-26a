<?php

namespace App\Models\RBAC;

use App\Enums\UserRole;
use App\Models\Traits\ModelDocBlocks;
use App\Models\Traits\Searchable;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * Role model for RBAC (Role-Based Access Control).
 *
 * Extends Spatie's Permission Role model with UUID support and search capabilities.
 * Manages role definitions and their associations with permissions and users.
 *
 * @property string $id UUID primary key
 * @property string $name Role name
 * @property string $guard_name Authentication guard (web, sanctum, etc.)
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @method static Role findOrCreate(string $name, string $guardName)
 */
class Role extends \Spatie\Permission\Models\Role
{
    use HasUuids;
    use Searchable;
    use ModelDocBlocks;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rbac_roles';

    /**
     * The attributes that are searchable from a single keyword.
     *
     * @var list<string>
     */
    protected array $searchableFields = [
        'name',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string)Str::uuid();
            }
        });
    }

    /**
     * Get users assigned to this role.
     */
    public function users(): BelongsToMany
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');

        return $this->belongsToMany(
            User::class,
            $tableNames['model_has_roles'],
            $columnNames['role_pivot_key'] ?? 'role_id',
            $columnNames['model_pivot_key'] ?? 'model_id'
        )->where($tableNames['model_has_roles'] . '.model_type', User::class);
    }

    /**
     * Check if this role is protected and cannot be edited.
     */
    public function isProtected(): bool
    {
        return in_array($this->name, UserRole::values());
    }
}
