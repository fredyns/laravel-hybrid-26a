<?php

namespace App\Features\Rbac;

use App\Models\RBAC\Permission;
use Illuminate\Support\Str;

/**
 * Permissions of one guard, grouped by resource prefix and ordered by the
 * conventional CRUD action sequence.
 *
 * Shared by the permissions API endpoint and the role create/edit forms.
 */
class GroupedPermissions
{
    /** @var list<string> */
    private const ACTION_ORDER = ['index', 'show', 'create', 'update', 'delete'];

    /**
     * @return array<string, list<string>>
     */
    public function forGuard(string $guard): array
    {
        $groups = [];

        foreach (Permission::query()->where('guard_name', $guard)->pluck('name') as $name) {
            $name = (string) $name;
            $groups[Str::before($name, '.')][] = $name;
        }

        foreach ($groups as $resource => $names) {
            usort($names, fn (string $a, string $b): int => $this->actionRank($a) <=> $this->actionRank($b));

            $groups[$resource] = $names;
        }

        ksort($groups);

        return $groups;
    }

    private function actionRank(string $name): int
    {
        $index = array_search(Str::after($name, '.'), self::ACTION_ORDER, true);

        return $index === false ? count(self::ACTION_ORDER) : $index;
    }
}
