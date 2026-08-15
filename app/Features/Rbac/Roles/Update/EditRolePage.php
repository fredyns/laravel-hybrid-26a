<?php

namespace App\Features\Rbac\Roles\Update;

use App\Enums\AuthGuard;
use App\Features\Rbac\GroupedPermissions;
use App\Models\RBAC\Role;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Edit role')]
class EditRolePage extends Component
{
    use AuthorizesRequests;

    public Role $role;

    public string $name = '';

    public string $guard = '';

    /** @var list<string> */
    public array $permissions = [];

    public function mount(Role $role): void
    {
        $this->role = $role;
        $this->name = $role->name;
        $this->guard = $role->guard_name;
        $this->permissions = array_values(
            $role->permissions
                ->pluck('name')
                ->map(fn (mixed $name): string => (string) $name)
                ->all()
        );
    }

    /**
     * Permissions are guard-scoped, so a guard change invalidates the selection.
     */
    public function updatedGuard(): void
    {
        $this->permissions = [];
    }

    public function save(UpdateRole $action): void
    {
        $this->authorize('rbac.update');

        /** @var array{name: string, guard: string, permissions?: list<string>} $validated */
        $validated = $this->validate(UpdateRoleRequest::rulesFor($this->guard, $this->role));

        $action->execute($this->role, $validated);

        Flux::toast(variant: 'success', text: __('Role updated.'));

        $this->redirectRoute('roles.show', $this->role);
    }

    public function render(): View
    {
        return view('features::Rbac.Roles.Update.edit-role-page', [
            'guards' => AuthGuard::values(),
            'permissionGroups' => app(GroupedPermissions::class)->forGuard($this->guard),
        ]);
    }
}
