<?php

use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Users')] class extends Component {
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

    public function delete(string $userId): void
    {
        $this->authorize('users.delete');

        User::query()->findOrFail($userId)->delete();

        Flux::toast(variant: 'success', text: __('User deleted.'));
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
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ __('Users') }}</flux:heading>
            <flux:subheading>{{ __('Manage application users.') }}</flux:subheading>
        </div>

        @can('users.create')
            <flux:button variant="primary" icon="plus" wire:click="create">
                {{ __('New user') }}
            </flux:button>
        @endcan
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <flux:input
            class="max-w-xs"
            wire:model.live.debounce.300ms="search"
            icon="magnifying-glass"
            :placeholder="__('Search name or email...')"
            clearable
        />

        <flux:spacer />

        <flux:select class="max-w-[7rem]" wire:model.live="perPage">
            <flux:select.option value="10">10</flux:select.option>
            <flux:select.option value="25">25</flux:select.option>
            <flux:select.option value="50">50</flux:select.option>
        </flux:select>

        <flux:dropdown>
            <flux:button icon="view-columns" icon:trailing="chevron-down">{{ __('Columns') }}</flux:button>

            <flux:menu>
                <flux:menu.checkbox.group wire:model.live="visibleColumns">
                    @foreach ($this->columns() as $key => $label)
                        <flux:menu.checkbox value="{{ $key }}">{{ $label }}</flux:menu.checkbox>
                    @endforeach
                </flux:menu.checkbox.group>
            </flux:menu>
        </flux:dropdown>
    </div>

    <flux:table :paginate="$this->users">
        <flux:table.columns>
            @foreach ($this->columns() as $key => $label)
                @if (in_array($key, $visibleColumns, true))
                    <flux:table.column
                        sortable
                        :sorted="$sortColumn === $key"
                        :direction="$sortDirection"
                        wire:click="sort('{{ $key }}')"
                    >
                        {{ $label }}
                    </flux:table.column>
                @endif
            @endforeach
            <flux:table.column align="end">{{ __('Actions') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->users as $user)
                <flux:table.row :key="$user->id">
                    @if (in_array('name', $visibleColumns, true))
                        <flux:table.cell variant="strong">{{ $user->name }}</flux:table.cell>
                    @endif

                    @if (in_array('email', $visibleColumns, true))
                        <flux:table.cell>{{ $user->email }}</flux:table.cell>
                    @endif

                    @if (in_array('email_verified_at', $visibleColumns, true))
                        <flux:table.cell>
                            @if ($user->email_verified_at)
                                <flux:badge color="green" size="sm">{{ __('Verified') }}</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm">{{ __('Unverified') }}</flux:badge>
                            @endif
                        </flux:table.cell>
                    @endif

                    @if (in_array('created_at', $visibleColumns, true))
                        <flux:table.cell>{{ $user->created_at?->format('Y-m-d H:i') }}</flux:table.cell>
                    @endif

                    @if (in_array('updated_at', $visibleColumns, true))
                        <flux:table.cell>{{ $user->updated_at?->format('Y-m-d H:i') }}</flux:table.cell>
                    @endif

                    <flux:table.cell align="end">
                        <div class="flex justify-end gap-1">
                            <flux:tooltip :content="__('View')">
                                <flux:button size="sm" variant="ghost" icon="eye" wire:click="viewUser('{{ $user->id }}')" :aria-label="__('View')" />
                            </flux:tooltip>

                            <flux:tooltip :content="__('Open page')">
                                <flux:button size="sm" variant="ghost" icon="arrow-up-right" :href="route('users.show', $user)" wire:navigate :aria-label="__('Open page')" />
                            </flux:tooltip>

                            @can('users.update')
                                <flux:tooltip :content="__('Edit')">
                                    <flux:button size="sm" variant="ghost" icon="pencil-square" wire:click="edit('{{ $user->id }}')" :aria-label="__('Edit')" />
                                </flux:tooltip>
                            @endcan

                            @can('users.change-password')
                                <flux:tooltip :content="__('Change password')">
                                    <flux:button size="sm" variant="ghost" icon="key" wire:click="changePassword('{{ $user->id }}')" :aria-label="__('Change password')" />
                                </flux:tooltip>
                            @endcan

                            @can('users.delete')
                                <flux:tooltip :content="__('Delete')">
                                    <flux:button
                                        size="sm"
                                        variant="ghost"
                                        icon="trash"
                                        wire:click="delete('{{ $user->id }}')"
                                        wire:confirm="{{ __('Delete this user? This cannot be undone.') }}"
                                        :aria-label="__('Delete')"
                                    />
                                </flux:tooltip>
                            @endcan
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="{{ count(array_intersect(array_keys($this->columns()), $visibleColumns)) + 1 }}">
                        <div class="py-6 text-center">
                            <flux:text>{{ __('No users found.') }}</flux:text>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <livewire:pages::users.form />
    <livewire:pages::users.show-modal />
    <livewire:pages::users.change-password />
</div>
