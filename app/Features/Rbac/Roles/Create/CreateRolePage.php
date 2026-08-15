<?php

namespace App\Features\Rbac\Roles\Create;

use App\Enums\AuthGuard;
use App\Features\Rbac\GroupedPermissions;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Create role')]
class CreateRolePage extends Component
{
    use AuthorizesRequests;

    public string $name = '';

    public string $guard = '';

    /** @var list<string> */
    public array $permissions = [];

    public function mount(): void
    {
        $this->guard = AuthGuard::values()[0] ?? 'web';
    }

    /**
     * Permissions are guard-scoped, so a guard change invalidates the selection.
     */
    public function updatedGuard(): void
    {
        $this->permissions = [];
    }

    public function save(CreateRole $action): void
    {
        $this->authorize('rbac.create');

        /** @var array{name: string, guard: string, permissions?: list<string>} $validated */
        $validated = $this->validate(CreateRoleRequest::rulesFor($this->guard));

        $role = $action->execute($validated);

        Flux::toast(variant: 'success', text: __('Role created.'));

        $this->redirectRoute('roles.show', $role);
    }

    public function render(): View
    {
        return view('features::Rbac.Roles.Create.create-role-page', [
            'guards' => AuthGuard::values(),
            'permissionGroups' => app(GroupedPermissions::class)->forGuard($this->guard),
        ]);
    }
}
