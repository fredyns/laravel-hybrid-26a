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

        <div class="flex items-center gap-1.5">
            <flux:button
                size="sm"
                :variant="$guard === '' ? 'primary' : 'outline'"
                class="rounded-full"
                wire:click="filterByGuard('')"
            >
                {{ __('All') }}
            </flux:button>

            @foreach ($this->guards() as $guardValue)
                <flux:button
                    size="sm"
                    :variant="$guard === $guardValue ? 'primary' : 'outline'"
                    class="rounded-full"
                    wire:click="filterByGuard('{{ $guardValue }}')"
                >
                    {{ $guardValue }}
                </flux:button>
            @endforeach
        </div>

        <flux:spacer />

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

    <livewire:pages.roles.form />
</div>
