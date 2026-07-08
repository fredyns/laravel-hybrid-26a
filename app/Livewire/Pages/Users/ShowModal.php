<?php

namespace App\Livewire\Pages\Users;

use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;

class ShowModal extends Component
{
    public ?User $user = null;

    #[On('user-view')]
    public function showUser(string $userId): void
    {
        $this->authorize('users.show');

        $this->user = User::query()->findOrFail($userId);

        $this->modal('user-show')->show();
    }

    public function render()
    {
        return view('livewire.pages.users.show-modal');
    }
}
