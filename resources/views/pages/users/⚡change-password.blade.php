<?php

use App\Concerns\PasswordValidationRules;
use App\Models\User;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    use PasswordValidationRules;

    public ?string $userId = null;

    public string $userName = '';

    public string $password = '';

    public string $password_confirmation = '';

    #[On('user-change-password')]
    public function open(string $userId): void
    {
        $this->authorize('users.change-password');

        $user = User::query()->findOrFail($userId);

        $this->reset(['password', 'password_confirmation']);
        $this->resetValidation();
        $this->userId = $user->id;
        $this->userName = $user->name;

        $this->modal('user-change-password')->show();
    }

    public function updatePassword(): void
    {
        $this->authorize('users.change-password');

        $validated = $this->validate([
            'password' => $this->passwordRules(),
        ]);

        $user = User::query()->findOrFail($this->userId);
        $user->update([
            'password' => $validated['password'],
        ]);

        $this->reset(['password', 'password_confirmation']);

        Flux::toast(variant: 'success', text: __('Password updated.'));

        $this->modal('user-change-password')->close();
    }
}; ?>

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
