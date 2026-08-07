<?php

namespace App\Features\User\Create;

use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Create user')]
class CreateUserPage extends Component
{
    use AuthorizesRequests;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function save(CreateUser $action): void
    {
        $this->authorize('users.create');

        $this->validate(CreateUserRequest::rulesFor());

        $user = $action->execute([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ]);

        Flux::toast(variant: 'success', text: __('User created.'));

        $this->redirectRoute('users.show', $user);
    }

    public function render(): View
    {
        return view('features::User.Create.create-user-page');
    }
}
