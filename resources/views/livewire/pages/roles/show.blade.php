<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <flux:button :href="route('roles.index')" wire:navigate variant="ghost" icon="arrow-left" size="sm" />
            <div>
                <flux:heading size="xl">{{ $role->name }}</flux:heading>
                <flux:subheading>
                    <span class="inline-flex items-center gap-2">
                        <flux:badge color="zinc" size="sm">{{ $role->guard_name }}</flux:badge>
                        @if ($role->isProtected())
                            <flux:badge color="amber" size="sm">{{ __('Protected') }}</flux:badge>
                        @else
                            <flux:badge color="zinc" size="sm">{{ __('Custom') }}</flux:badge>
                        @endif
                    </span>
                </flux:subheading>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @can('rbac.update')
                <flux:button wire:click="edit" variant="primary" icon="pencil-square">
                    {{ __('Edit') }}
                </flux:button>
            @endcan

            @can('rbac.delete')
                @unless ($role->isProtected())
                    <flux:button
                        wire:click="delete"
                        wire:confirm="{{ __('Delete this role? This cannot be undone.') }}"
                        variant="danger"
                        icon="trash"
                    >
                        {{ __('Delete') }}
                    </flux:button>
                @endunless
            @endcan
        </div>
    </div>

    <flux:separator />

    <div class="grid max-w-2xl grid-cols-1 gap-6 sm:grid-cols-2">
        <div>
            <flux:text size="sm" class="text-zinc-500">{{ __('Name') }}</flux:text>
            <flux:text class="font-medium">{{ $role->name }}</flux:text>
        </div>

        <div>
            <flux:text size="sm" class="text-zinc-500">{{ __('Guard') }}</flux:text>
            <flux:text class="font-medium">{{ $role->guard_name }}</flux:text>
        </div>

        <div>
            <flux:text size="sm" class="text-zinc-500">{{ __('Assigned users') }}</flux:text>
            <flux:text class="font-medium">{{ $role->users()->count() }}</flux:text>
        </div>

        <div>
            <flux:text size="sm" class="text-zinc-500">{{ __('Created') }}</flux:text>
            <flux:text class="font-medium">{{ $role->created_at?->format('Y-m-d H:i') }}</flux:text>
        </div>
    </div>

    <flux:separator />

    <div class="space-y-4">
        <div class="flex items-center gap-2">
            <flux:heading size="lg">{{ __('Permissions') }}</flux:heading>
            <flux:badge color="zinc" size="sm">{{ $role->permissions->count() }}</flux:badge>
        </div>

        @forelse ($this->permissionGroups as $resource => $permissions)
            <div class="space-y-2">
                <flux:text class="font-semibold">{{ Str::headline($resource) }}</flux:text>
                <div class="flex flex-wrap gap-2">
                    @foreach ($permissions as $permission)
                        <flux:badge color="zinc" size="sm">{{ Str::headline(Str::after($permission, '.')) }}</flux:badge>
                    @endforeach
                </div>
            </div>
        @empty
            <flux:text>{{ __('This role has no permissions assigned.') }}</flux:text>
        @endforelse
    </div>

    <livewire:pages::roles.form />
</div>
