<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('users.index') }}">
            <flux:button variant="ghost" icon="arrow-left" size="sm" />
        </a>
        <div>
            <flux:heading size="xl">{{ __('New user') }}</flux:heading>
            <flux:subheading>{{ __('Create a new application user.') }}</flux:subheading>
        </div>
    </div>

    <flux:separator />

    <form wire:submit="save" class="w-full max-w-lg space-y-6">
        <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />

        <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

        <flux:input wire:model="password" :label="__('Password')" type="password" required autocomplete="new-password" viewable />

        <flux:input wire:model="password_confirmation" :label="__('Confirm password')" type="password" required autocomplete="new-password" viewable />

        <div class="flex items-center gap-4">
            <flux:button variant="primary" type="submit" data-test="create-user-button">
                {{ __('Create user') }}
            </flux:button>

            <flux:button variant="ghost" href="{{ route('users.index') }}">
                {{ __('Cancel') }}
            </flux:button>
        </div>
    </form>
</div>
