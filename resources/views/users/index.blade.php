<x-layouts::app :title="__('Users')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ __('Users') }}</flux:heading>
            <flux:subheading>{{ __('Manage application users.') }}</flux:subheading>
        </div>

        @can('users.create')
            <flux:button variant="primary" icon="plus" href="{{ route('users.index') }}" class="cursor-not-allowed opacity-50">
                {{ __('New user') }}
            </flux:button>
        @endcan
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <form method="GET" class="flex flex-wrap items-center gap-3 flex-1">
            <flux:input
                class="max-w-xs"
                name="q"
                value="{{ $search }}"
                icon="magnifying-glass"
                :placeholder="__('Search name or email...')"
                onchange="this.form.submit()"
            />

            <flux:spacer />

            <select name="per_page" onchange="this.form.submit()" class="max-w-[7rem] rounded border border-gray-300 px-2 py-1 text-sm">
                <option value="10" {{ $perPage === 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ $perPage === 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ $perPage === 50 ? 'selected' : '' }}>50</option>
            </select>

            <flux:dropdown>
                <flux:button icon="view-columns" icon:trailing="chevron-down">{{ __('Columns') }}</flux:button>

                <flux:menu>
                    <div class="p-2 space-y-2">
                        @foreach ($columns as $key => $label)
                            <label class="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    name="columns[]"
                                    value="{{ $key }}"
                                    {{ in_array($key, $visibleColumns) ? 'checked' : '' }}
                                    onchange="this.form.submit()"
                                />
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </flux:menu>
            </flux:dropdown>
        </form>
    </div>

    <flux:table>
        <flux:table.columns>
            @foreach ($columns as $key => $label)
                @if (in_array($key, $visibleColumns, true))
                    <flux:table.column>
                        <a href="{{ request()->url() }}?{{ http_build_query(array_merge(request()->query(), ['sort' => $key, 'direction' => $sortColumn === $key && $sortDirection === 'asc' ? 'desc' : 'asc'])) }}" class="hover:underline">
                            {{ $label }}
                            @if ($sortColumn === $key)
                                {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                            @endif
                        </a>
                    </flux:table.column>
                @endif
            @endforeach
            <flux:table.column align="end">{{ __('Actions') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($users as $user)
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
                                <a href="{{ route('users.show', $user) }}">
                                    <flux:button size="sm" variant="ghost" icon="eye" :aria-label="__('View')" />
                                </a>
                            </flux:tooltip>

                            @can('users.update')
                                <flux:tooltip :content="__('Edit')">
                                    <span class="cursor-not-allowed opacity-50">
                                        <flux:button size="sm" variant="ghost" icon="pencil-square" :aria-label="__('Edit')" />
                                    </span>
                                </flux:tooltip>
                            @endcan

                            @can('users.change-password')
                                <flux:tooltip :content="__('Change password')">
                                    <span class="cursor-not-allowed opacity-50">
                                        <flux:button size="sm" variant="ghost" icon="key" :aria-label="__('Change password')" />
                                    </span>
                                </flux:tooltip>
                            @endcan
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="{{ count(array_intersect(array_keys($columns), $visibleColumns)) + 1 }}">
                        <div class="py-6 text-center">
                            <flux:text>{{ __('No users found.') }}</flux:text>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    {{ $users->links() }}
    </div>
</x-layouts::app>
