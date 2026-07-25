<x-layouts::app :title="$role->name">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('roles.index') }}">
                <flux:button variant="ghost" icon="arrow-left" size="sm" />
            </a>
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
                <span class="cursor-not-allowed opacity-50">
                    <flux:button variant="primary" icon="pencil-square">
                        {{ __('Edit') }}
                    </flux:button>
                </span>
            @endcan

            @can('rbac.delete')
                @unless ($role->isProtected())
                    <span class="cursor-not-allowed opacity-50">
                        <flux:button variant="danger" icon="trash">
                            {{ __('Delete') }}
                        </flux:button>
                    </span>
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

        @forelse ($permissionGroups as $resource => $permissions)
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
    </div>
</x-layouts::app>
