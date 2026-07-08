<?php

namespace App\Livewire\Pages\Users;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class Form extends Component
{
    use PasswordValidationRules;
    use ProfileValidationRules;

    public ?string $userId = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function isEditing(): bool
    {
        return $this->userId !== null;
    }

    #[On('user-create')]
    public function createUser(): void
    {
        $this->authorize('users.create');

        $this->resetForm();

        $this->modal('user-form')->show();
    }

    #[On('user-edit')]
    public function editUser(string $userId): void
    {
        $this->authorize('users.update');

        $user = User::query()->findOrFail($userId);

        $this->resetForm();
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;

        $this->modal('user-form')->show();
    }

    public function save(): void
    {
        $this->authorize($this->isEditing() ? 'users.update' : 'users.create');

        $validated = $this->validate($this->rules());

        if ($this->isEditing()) {
            $user = User::query()->findOrFail($this->userId);
            $user->fill([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();
        } else {
            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]);
        }

        Flux::toast(variant: 'success', text: $this->isEditing() ? __('User updated.') : __('User created.'));

        $this->dispatch('users-updated');

        $this->modal('user-form')->close();

        $this->resetForm();
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        $rules = [
            'name' => $this->nameRules(),
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->userId),
            ],
        ];

        if (! $this->isEditing()) {
            $rules['password'] = $this->passwordRules();
        }

        return $rules;
    }

    protected function resetForm(): void
    {
        $this->reset(['userId', 'name', 'email', 'password', 'password_confirmation']);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.pages.users.form');
    }
}
