<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('users.show', $user) }}">
            <flux:button variant="ghost" icon="arrow-left" size="sm" />
        </a>
        <div>
            <flux:heading size="xl">{{ __('Edit user') }}</flux:heading>
            <flux:subheading>{{ $user->email }}</flux:subheading>
        </div>
    </div>

    <flux:separator />

    <form wire:submit="save" class="w-full max-w-lg space-y-6">
        <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />

        <div>
            <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

            <flux:text size="sm" class="mt-2 text-zinc-500">
                {{ __('Changing the email will reset its verification status.') }}
            </flux:text>
        </div>

        <div class="flex items-center gap-4">
            <flux:button variant="primary" type="submit" data-test="update-user-button">
                {{ __('Save changes') }}
            </flux:button>

            <flux:button variant="ghost" href="{{ route('users.show', $user) }}">
                {{ __('Cancel') }}
            </flux:button>
        </div>
    </form>
</div>
