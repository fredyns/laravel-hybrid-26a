<?php

use App\Enums\AuthGuard;
use App\Models\RBAC\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Roles')] class extends Component {
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

    public function updatedGuard(): void
    {
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
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ __('Roles') }}</flux:heading>
            <flux:subheading>{{ __('Manage roles and their permissions.') }}</flux:subheading>
        </div>

        @can('rbac.create')
            <flux:button variant="primary" icon="plus" wire:click="create">
                {{ __('New role') }}
            </flux:button>
        @endcan
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <flux:input
            class="max-w-xs"
            wire:model.live.debounce.300ms="search"
            icon="magnifying-glass"
            :placeholder="__('Search roles...')"
            clearable
        />

        <flux:spacer />

        <flux:select class="max-w-[10rem]" wire:model.live="guard">
            <flux:select.option value="">{{ __('All guards') }}</flux:select.option>
            @foreach ($this->guards() as $guardValue)
                <flux:select.option value="{{ $guardValue }}">{{ $guardValue }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:select class="max-w-[7rem]" wire:model.live="perPage">
            <flux:select.option value="10">10</flux:select.option>
            <flux:select.option value="25">25</flux:select.option>
            <flux:select.option value="50">50</flux:select.option>
        </flux:select>
    </div>

    <flux:table :paginate="$this->roles">
        <flux:table.columns>
            <flux:table.column>{{ __('Name') }}</flux:table.column>
            <flux:table.column>{{ __('Guard') }}</flux:table.column>
            <flux:table.column>{{ __('Permissions') }}</flux:table.column>
            <flux:table.column>{{ __('Type') }}</flux:table.column>
            <flux:table.column>{{ __('Created') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Actions') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->roles as $role)
                <flux:table.row :key="$role->id">
                    <flux:table.cell variant="strong">
                        <a href="{{ route('roles.show', $role) }}" wire:navigate class="hover:underline">{{ $role->name }}</a>
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:badge color="zinc" size="sm">{{ $role->guard_name }}</flux:badge>
                    </flux:table.cell>

                    <flux:table.cell>
                        <span class="inline-flex items-center gap-1.5">
                            <flux:icon.shield-check class="size-4 text-zinc-400" />
                            {{ $role->permissions_count }}
                        </span>
                    </flux:table.cell>

                    <flux:table.cell>
                        @if ($role->isProtected())
                            <flux:badge color="amber" size="sm">{{ __('Protected') }}</flux:badge>
                        @else
                            <flux:badge color="zinc" size="sm">{{ __('Custom') }}</flux:badge>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell>{{ $role->created_at?->format('Y-m-d H:i') }}</flux:table.cell>

                    <flux:table.cell align="end">
                        <div class="flex justify-end gap-1">
                            <flux:tooltip :content="__('View')">
                                <flux:button size="sm" variant="ghost" icon="eye" :href="route('roles.show', $role)" wire:navigate :aria-label="__('View')" />
                            </flux:tooltip>

                            @can('rbac.update')
                                <flux:tooltip :content="__('Edit')">
                                    <flux:button size="sm" variant="ghost" icon="pencil-square" wire:click="edit('{{ $role->id }}')" :aria-label="__('Edit')" />
                                </flux:tooltip>
                            @endcan
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="6">
                        <div class="py-6 text-center">
                            <flux:text>{{ __('No roles found.') }}</flux:text>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <livewire:pages::roles.form />
</div>
