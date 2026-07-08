<flux:modal name="user-form" class="w-full max-w-lg">
    <form wire:submit="save" class="space-y-6">
        <div>
            <flux:heading size="lg">{{ $this->isEditing() ? __('Edit user') : __('Create user') }}</flux:heading>
            <flux:subheading>
                {{ $this->isEditing() ? __('Update this user\'s details.') : __('Add a new user to the application.') }}
            </flux:subheading>
        </div>

        <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="off" />

        <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="off" />

        @unless ($this->isEditing())
            <flux:input wire:model="password" :label="__('Password')" type="password" viewable required autocomplete="new-password" />

            <flux:input wire:model="password_confirmation" :label="__('Confirm password')" type="password" viewable required autocomplete="new-password" />
        @endunless

        <div class="flex justify-end gap-2">
            <flux:modal.close>
                <flux:button variant="filled" type="button">{{ __('Cancel') }}</flux:button>
            </flux:modal.close>

            <flux:button variant="primary" type="submit">
                {{ $this->isEditing() ? __('Save changes') : __('Create user') }}
            </flux:button>
        </div>
    </form>
</flux:modal>
