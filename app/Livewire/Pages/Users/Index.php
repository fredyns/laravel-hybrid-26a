<?php

namespace App\Livewire\Pages\Users;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Users')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    /**
     * The columns currently visible in the table.
     *
     * @var array<int, string>
     */
    #[Url]
    public array $visibleColumns = ['name', 'email', 'created_at'];

    public int $perPage = 10;

    public string $sortColumn = 'created_at';

    public string $sortDirection = 'desc';

    /**
     * All columns that may be toggled on the table.
     *
     * @return array<string, string>
     */
    public function columns(): array
    {
        return [
            'name' => __('Name'),
            'email' => __('Email'),
            'email_verified_at' => __('Verified'),
            'created_at' => __('Created'),
            'updated_at' => __('Updated'),
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function sort(string $column): void
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';

            return;
        }

        $this->sortColumn = $column;
        $this->sortDirection = 'asc';
    }

    public function create(): void
    {
        $this->authorize('users.create');

        $this->dispatch('user-create');
    }

    public function edit(string $userId): void
    {
        $this->authorize('users.update');

        $this->dispatch('user-edit', userId: $userId);
    }

    public function viewUser(string $userId): void
    {
        $this->authorize('users.show');

        $this->dispatch('user-view', userId: $userId);
    }

    public function changePassword(string $userId): void
    {
        $this->authorize('users.change-password');

        $this->dispatch('user-change-password', userId: $userId);
    }

    #[On('users-updated')]
    public function refreshList(): void
    {
        // Triggered by child modals after create/update so the list re-renders.
    }

    #[Computed]
    public function users(): LengthAwarePaginator
    {
        return User::query()
            ->when($this->search !== '', fn ($query) => $query->search($this->search))
            ->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.pages.users.index');
    }
}
