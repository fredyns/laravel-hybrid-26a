<?php

namespace App\Features\User\Update;

use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Edit user')]
class EditUserPage extends Component
{
    use AuthorizesRequests;

    public User $user;

    public string $name = '';

    public string $email = '';

    public function mount(User $user): void
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
    }

    public function save(UpdateUser $action): void
    {
        $this->authorize('users.update');

        $this->validate(UpdateUserRequest::rulesFor($this->user));

        $action->execute($this->user, [
            'name' => $this->name,
            'email' => $this->email,
        ]);

        Flux::toast(variant: 'success', text: __('User updated.'));

        $this->redirectRoute('users.show', $this->user);
    }

    public function render(): View
    {
        return view('features::User.Update.edit-user-page');
    }
}
