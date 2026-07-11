<?php

namespace App\Http\Controllers\Api\Roles;

use App\Enums\AuthGuard;
use App\Http\Controllers\Controller;
use App\Models\RBAC\Permission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PermissionIndexController extends Controller
{
    /**
     * Permissions for a guard, grouped by resource prefix and ordered by the
     * conventional CRUD action sequence — mirrors the Livewire role form.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'guard' => ['required', 'string', Rule::in(AuthGuard::values())],
        ]);

        $order = ['index', 'show', 'create', 'update', 'delete'];

        $groups = Permission::query()
            ->where('guard_name', $validated['guard'])
            ->pluck('name')
            ->groupBy(fn (string $name) => Str::before($name, '.'))
            ->map(fn ($group) => $group
                ->sortBy(function (string $name) use ($order) {
                    $action = Str::after($name, '.');
                    $index = array_search($action, $order, true);

                    return $index === false ? count($order) : $index;
                })
                ->values()
                ->all())
            ->sortKeys();

        return response()->json(['data' => $groups]);
    }
}
