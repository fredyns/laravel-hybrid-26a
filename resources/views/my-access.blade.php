<x-layouts::app :title="__('My Access')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div>
            <flux:heading size="xl">{{ __('My Access') }}</flux:heading>
            <flux:subheading>{{ __('Roles and permissions assigned to your account.') }}</flux:subheading>
        </div>

        <flux:separator />

        <div class="space-y-4">
            <div class="flex items-center gap-2">
                <flux:heading size="lg">{{ __('Roles') }}</flux:heading>
                <flux:badge color="zinc" size="sm">{{ $roles->count() }}</flux:badge>
            </div>

            <div class="flex flex-wrap gap-2">
                @forelse ($roles as $role)
                    <flux:badge color="blue" size="sm">{{ Str::headline($role->name) }}</flux:badge>
                @empty
                    <flux:text>{{ __('You have no roles assigned.') }}</flux:text>
                @endforelse
            </div>
        </div>

        <flux:separator />

        <div class="space-y-4">
            <div class="flex items-center gap-2">
                <flux:heading size="lg">{{ __('Permissions') }}</flux:heading>
                <flux:badge color="zinc" size="sm">{{ $permissionGroups->flatten()->count() }}</flux:badge>
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
                <flux:text>{{ __('You have no permissions assigned.') }}</flux:text>
            @endforelse
        </div>
    </div>
</x-layouts::app>
