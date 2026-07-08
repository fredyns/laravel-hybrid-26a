<?php

namespace App\Livewire\Pages\Users;

use App\Concerns\PasswordValidationRules;
use App\Models\User;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Component;

class ChangePassword extends Component
{
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

    public function render()
    {
        return view('livewire.pages.users.change-password');
    }
}
