<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ __('Roles') }}</flux:heading>
            <flux:subheading>{{ __('Manage roles and their permissions.') }}</flux:subheading>
        </div>

        @can('rbac.create')
            <flux:button variant="primary" icon="plus" href="{{ route('roles.create') }}">
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
        />

        <div class="flex items-center gap-1.5">
            <flux:button
                size="sm"
                :variant="$activeGuard === '' ? 'primary' : 'outline'"
                class="rounded-full"
                wire:click="filterByGuard('')"
            >
                {{ __('All') }}
            </flux:button>

            @foreach ($guards as $guardValue)
                <flux:button
                    size="sm"
                    :variant="$activeGuard === $guardValue ? 'primary' : 'outline'"
                    class="rounded-full"
                    wire:click="filterByGuard('{{ $guardValue }}')"
                    wire:key="guard-{{ $guardValue }}"
                >
                    {{ $guardValue }}
                </flux:button>
            @endforeach
        </div>

        <flux:spacer />

        <select wire:model.live="perPage" class="max-w-[7rem] rounded border border-gray-300 px-2 py-1 text-sm">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
        </select>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('Name') }}</flux:table.column>
            <flux:table.column>{{ __('Guard') }}</flux:table.column>
            <flux:table.column>{{ __('Permissions') }}</flux:table.column>
            <flux:table.column>{{ __('Type') }}</flux:table.column>
            <flux:table.column>{{ __('Created') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Actions') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($roles as $role)
                <flux:table.row :key="$role->id" wire:key="role-{{ $role->id }}">
                    <flux:table.cell variant="strong">
                        <a href="{{ route('roles.show', $role) }}" class="hover:underline" wire:navigate>{{ $role->name }}</a>
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
                                <a href="{{ route('roles.show', $role) }}" wire:navigate>
                                    <flux:button size="sm" variant="ghost" icon="eye" :aria-label="__('View')" />
                                </a>
                            </flux:tooltip>

                            @can('rbac.update')
                                <flux:tooltip :content="__('Edit')">
                                    <a href="{{ route('roles.edit', $role) }}" wire:navigate>
                                        <flux:button size="sm" variant="ghost" icon="pencil-square" :aria-label="__('Edit')" />
                                    </a>
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

    {{ $roles->links() }}
</div>
