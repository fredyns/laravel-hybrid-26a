<?php

namespace App\Features\User\Index;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Users')]
class IndexUsersPage extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $sort = 'created_at';

    #[Url]
    public string $direction = 'desc';

    #[Url(as: 'per_page')]
    public int $perPage = 10;

    /** @var list<string> */
    public array $visibleColumns = ['name', 'email', 'created_at'];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $column): void
    {
        if (! array_key_exists($column, $this->columns())) {
            return;
        }

        if ($this->sort === $column) {
            $this->direction = $this->direction === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sort = $column;
            $this->direction = 'asc';
        }

        $this->resetPage();
    }

    /** @return array<string, string> */
    private function columns(): array
    {
        return [
            'name' => __('Name'),
            'email' => __('Email'),
            'email_verified_at' => __('Verified'),
            'created_at' => __('Created'),
            'updated_at' => __('Updated'),
        ];
    }

    public function render(): View
    {
        $columns = $this->columns();

        $sortColumn = array_key_exists($this->sort, $columns) ? $this->sort : 'created_at';
        $sortDirection = $this->direction === 'asc' ? 'asc' : 'desc';

        $users = User::query()
            ->when($this->search !== '', fn ($query) => $query->search($this->search))
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(max(1, min(100, $this->perPage)));

        return view('features::User.Index.index-users-page', compact(
            'users',
            'columns',
            'sortColumn',
            'sortDirection',
        ));
    }
}
