<?php

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
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
}; ?>

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
