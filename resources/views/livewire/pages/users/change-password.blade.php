<flux:modal name="user-change-password" class="w-full max-w-lg">
    <form wire:submit="updatePassword" class="space-y-6">
        <div>
            <flux:heading size="lg">{{ __('Change password') }}</flux:heading>
            <flux:subheading>
                {{ $userName ? __('Set a new password for :name.', ['name' => $userName]) : __('Set a new password.') }}
            </flux:subheading>
        </div>

        <flux:input wire:model="password" :label="__('New password')" type="password" viewable required autocomplete="new-password" />

        <flux:input wire:model="password_confirmation" :label="__('Confirm password')" type="password" viewable required autocomplete="new-password" />

        <div class="flex justify-end gap-2">
            <flux:modal.close>
                <flux:button variant="filled" type="button">{{ __('Cancel') }}</flux:button>
            </flux:modal.close>

            <flux:button variant="primary" type="submit">{{ __('Update password') }}</flux:button>
        </div>
    </form>
</flux:modal>
