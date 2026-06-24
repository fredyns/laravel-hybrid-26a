<?php

namespace App\Models\RBAC;

use App\Models\Traits\ModelDocBlocks;
use App\Models\Traits\Searchable;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * Permission model for RBAC (Role-Based Access Control).
 *
 * Extends Spatie's Permission model with UUID support and search capabilities.
 * Defines granular permissions that can be assigned to roles and users.
 *
 * @property string $id UUID primary key
 * @property string $name Permission name
 * @property string $guard_name Authentication guard (web, sanctum, etc.)
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Permission extends \Spatie\Permission\Models\Permission
{
    use HasUuids;
    use Searchable;
    use ModelDocBlocks;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rbac_permissions';

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
     * Get users that have this permission assigned.
     */
    public function users(): BelongsToMany
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');

        return $this->belongsToMany(
            User::class,
            $tableNames['model_has_permissions'],
            $columnNames['permission_pivot_key'] ?? 'permission_id',
            $columnNames['model_morph_key'] ?? 'model_id'
        )->where($tableNames['model_has_permissions'] . '.model_type', User::class);
    }
}
