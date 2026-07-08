<?php

namespace App\Livewire\Pages\Users;

use App\Models\User;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('User')]
class Show extends Component
{
    public User $user;

    public function mount(User $user): void
    {
        $this->authorize('users.show');

        $this->user = $user;
    }

    public function edit(): void
    {
        $this->authorize('users.update');

        $this->dispatch('user-edit', userId: $this->user->id);
    }

    public function changePassword(): void
    {
        $this->authorize('users.change-password');

        $this->dispatch('user-change-password', userId: $this->user->id);
    }

    public function delete(): void
    {
        $this->authorize('users.delete');

        $this->user->delete();

        Flux::toast(variant: 'success', text: __('User deleted.'));

        $this->redirect(route('users.index'), navigate: true);
    }

    #[On('users-updated')]
    public function refreshUser(): void
    {
        $this->user->refresh();
    }

    public function render()
    {
        return view('livewire.pages.users.show');
    }
}
