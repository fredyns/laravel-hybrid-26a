<x-layouts::app :title="__('Roles')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ __('Roles') }}</flux:heading>
            <flux:subheading>{{ __('Manage roles and their permissions.') }}</flux:subheading>
        </div>

        @can('rbac.create')
            <span class="cursor-not-allowed opacity-50">
                <flux:button variant="primary" icon="plus">
                    {{ __('New role') }}
                </flux:button>
            </span>
        @endcan
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <form method="GET" class="flex flex-wrap items-center gap-3 flex-1">
            <flux:input
                class="max-w-xs"
                name="q"
                value="{{ $search }}"
                icon="magnifying-glass"
                :placeholder="__('Search roles...')"
                onchange="this.form.submit()"
            />

            <div class="flex items-center gap-1.5">
                <a href="{{ route('roles.index') }}" class="inline-flex">
                    <flux:button size="sm" :variant="$guard === '' ? 'primary' : 'outline'" class="rounded-full">
                        {{ __('All') }}
                    </flux:button>
                </a>

                @foreach ($guards as $guardValue)
                    <a href="{{ route('roles.index', array_merge(request()->query(), ['guard' => $guardValue])) }}" class="inline-flex">
                        <flux:button size="sm" :variant="$guard === $guardValue ? 'primary' : 'outline'" class="rounded-full">
                            {{ $guardValue }}
                        </flux:button>
                    </a>
                @endforeach
            </div>

            <flux:spacer />

            <select name="per_page" onchange="this.form.submit()" class="max-w-[7rem] rounded border border-gray-300 px-2 py-1 text-sm">
                <option value="10" {{ $perPage === 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ $perPage === 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ $perPage === 50 ? 'selected' : '' }}>50</option>
            </select>
        </form>
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
                <flux:table.row :key="$role->id">
                    <flux:table.cell variant="strong">
                        <a href="{{ route('roles.show', $role) }}" class="hover:underline">{{ $role->name }}</a>
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
                                <a href="{{ route('roles.show', $role) }}">
                                    <flux:button size="sm" variant="ghost" icon="eye" :aria-label="__('View')" />
                                </a>
                            </flux:tooltip>

                            @can('rbac.update')
                                <flux:tooltip :content="__('Edit')">
                                    <span class="cursor-not-allowed opacity-50">
                                        <flux:button size="sm" variant="ghost" icon="pencil-square" :aria-label="__('Edit')" />
                                    </span>
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
</x-layouts::app>
