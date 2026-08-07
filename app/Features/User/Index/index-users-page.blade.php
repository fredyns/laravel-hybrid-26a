<div class="flex h-full w-full flex-1 flex-col gap-6">
    @vite(['app/Features/User/Index/style.css'])

    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ __('Users') }}</flux:heading>
            <flux:subheading>{{ __('Manage application users.') }}</flux:subheading>
        </div>

        @can('users.create')
            <flux:button variant="primary" icon="plus" href="{{ route('users.create') }}">
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
        />

        <flux:spacer />

        <select wire:model.live="perPage" class="max-w-[7rem] rounded border border-gray-300 px-2 py-1 text-sm">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
        </select>

        <flux:dropdown>
            <flux:button icon="view-columns" icon:trailing="chevron-down">{{ __('Columns') }}</flux:button>

            <flux:menu>
                <div class="p-2 space-y-2">
                    @foreach ($columns as $key => $label)
                        <label class="flex items-center gap-2">
                            <input
                                type="checkbox"
                                wire:model.live="visibleColumns"
                                value="{{ $key }}"
                            />
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </flux:menu>
        </flux:dropdown>
    </div>

    <flux:table>
        <flux:table.columns>
            @foreach ($columns as $key => $label)
                @if (in_array($key, $visibleColumns, true))
                    <flux:table.column>
                        <button type="button" wire:click="sortBy('{{ $key }}')" class="user-index-sort hover:underline">
                            {{ $label }}
                            @if ($sortColumn === $key)
                                {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                            @endif
                        </button>
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
                                    <a href="{{ route('users.edit', $user) }}">
                                        <flux:button size="sm" variant="ghost" icon="pencil-square" :aria-label="__('Edit')" />
                                    </a>
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
