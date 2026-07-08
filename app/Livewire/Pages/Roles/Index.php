<?php

namespace App\Livewire\Pages\Roles;

use App\Enums\AuthGuard;
use App\Models\RBAC\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Roles')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(as: 'guard', except: '')]
    public string $guard = '';

    public int $perPage = 10;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function filterByGuard(string $guard): void
    {
        $this->guard = $guard;

        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->authorize('rbac.create');

        $this->dispatch('role-create');
    }

    public function edit(string $roleId): void
    {
        $this->authorize('rbac.update');

        $this->dispatch('role-edit', roleId: $roleId);
    }

    #[On('roles-updated')]
    public function refreshList(): void
    {
        // Triggered by the child modal after create/update so the list re-renders.
    }

    /**
     * @return array<int, string>
     */
    public function guards(): array
    {
        return AuthGuard::values();
    }

    #[Computed]
    public function roles(): LengthAwarePaginator
    {
        return Role::query()
            ->withCount(['permissions', 'users'])
            ->when($this->search !== '', fn ($query) => $query->search($this->search))
            ->when($this->guard !== '', fn ($query) => $query->where('guard_name', $this->guard))
            ->orderByDesc('created_at')
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.pages.roles.index');
    }
}
