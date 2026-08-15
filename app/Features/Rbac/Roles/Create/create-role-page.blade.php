<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('roles.index') }}" wire:navigate>
            <flux:button variant="ghost" icon="arrow-left" size="sm" />
        </a>
        <div>
            <flux:heading size="xl">{{ __('New role') }}</flux:heading>
            <flux:subheading>{{ __('Create a role and assign its permissions.') }}</flux:subheading>
        </div>
    </div>

    <flux:separator />

    <form wire:submit="save" class="w-full max-w-3xl space-y-6">
        <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus />

        <flux:select wire:model.live="guard" :label="__('Guard')" required>
            @foreach ($guards as $guardValue)
                <flux:select.option value="{{ $guardValue }}" wire:key="guard-{{ $guardValue }}">{{ $guardValue }}</flux:select.option>
            @endforeach
        </flux:select>

        <div class="space-y-4">
            <flux:heading size="lg">{{ __('Permissions') }}</flux:heading>

            @forelse ($permissionGroups as $resource => $groupPermissions)
                <div class="space-y-2" wire:key="group-{{ $guard }}-{{ $resource }}">
                    <flux:text class="font-semibold">{{ Str::headline($resource) }}</flux:text>

                    <div class="flex flex-wrap gap-4">
                        @foreach ($groupPermissions as $permission)
                            <flux:checkbox
                                wire:model="permissions"
                                value="{{ $permission }}"
                                :label="Str::headline(Str::after($permission, '.'))"
                                wire:key="permission-{{ $guard }}-{{ $permission }}"
                            />
                        @endforeach
                    </div>
                </div>
            @empty
                <flux:text>{{ __('No permissions available for this guard.') }}</flux:text>
            @endforelse

            <flux:error name="permissions" />
        </div>

        <div class="flex items-center gap-4">
            <flux:button variant="primary" type="submit" data-test="create-role-button">
                {{ __('Create role') }}
            </flux:button>

            <flux:button variant="ghost" href="{{ route('roles.index') }}">
                {{ __('Cancel') }}
            </flux:button>
        </div>
    </form>
</div>
