<?php

namespace App\Features\Rbac\Roles\Index;

use App\Enums\AuthGuard;
use App\Models\RBAC\Role;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Roles')]
class IndexRolesPage extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $guard = '';

    #[Url(as: 'per_page')]
    public int $perPage = 10;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function filterByGuard(string $guard): void
    {
        $this->guard = $this->normalizedGuard($guard);
        $this->resetPage();
    }

    private function normalizedGuard(string $guard): string
    {
        return in_array($guard, AuthGuard::values(), true) ? $guard : '';
    }

    public function render(): View
    {
        $guard = $this->normalizedGuard($this->guard);

        $roles = Role::query()
            ->withCount(['permissions', 'users'])
            ->when($this->search !== '', fn ($query) => $query->search($this->search))
            ->when($guard !== '', fn ($query) => $query->where('guard_name', $guard))
            ->orderByDesc('created_at')
            ->paginate(max(1, min(100, $this->perPage)));

        return view('features::Rbac.Roles.Index.index-roles-page', [
            'roles' => $roles,
            'guards' => AuthGuard::values(),
            'activeGuard' => $guard,
        ]);
    }
}
