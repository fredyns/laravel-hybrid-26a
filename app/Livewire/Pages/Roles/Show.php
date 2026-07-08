<?php

namespace App\Livewire\Pages\Roles;

use App\Models\RBAC\Role;
use Flux\Flux;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Role')]
class Show extends Component
{
    public Role $role;

    public function mount(Role $role): void
    {
        $this->authorize('rbac.show');

        $this->role = $role;
    }

    public function edit(): void
    {
        $this->authorize('rbac.update');

        $this->dispatch('role-edit', roleId: $this->role->id);
    }

    public function delete(): void
    {
        $this->authorize('rbac.delete');

        if ($this->role->isProtected()) {
            Flux::toast(variant: 'danger', text: __('Protected roles cannot be deleted.'));

            return;
        }

        if ($this->role->users()->count() > 0) {
            Flux::toast(variant: 'danger', text: __('Cannot delete a role that still has assigned users.'));

            return;
        }

        $this->role->delete();

        Flux::toast(variant: 'success', text: __('Role deleted.'));

        $this->redirect(route('roles.index'), navigate: true);
    }

    #[On('roles-updated')]
    public function refreshRole(): void
    {
        $this->role->refresh();
    }

    /**
     * The role's permissions grouped by resource prefix.
     *
     * @return Collection<string, Collection<int, string>>
     */
    #[Computed]
    public function permissionGroups(): Collection
    {
        return $this->role->permissions
            ->pluck('name')
            ->sort()
            ->groupBy(fn (string $name) => Str::before($name, '.'));
    }

    public function render()
    {
        return view('livewire.pages.roles.show');
    }
}
