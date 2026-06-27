<?php

use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public ?User $user = null;

    #[On('user-view')]
    public function showUser(string $userId): void
    {
        $this->authorize('users.show');

        $this->user = User::query()->findOrFail($userId);

        $this->modal('user-show')->show();
    }
}; ?>

<flux:modal name="user-show" class="w-full max-w-lg">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{ __('User details') }}</flux:heading>
            <flux:subheading>{{ __('Read-only view of the selected user.') }}</flux:subheading>
        </div>

        @if ($user)
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <flux:text size="sm" class="text-zinc-500">{{ __('Name') }}</flux:text>
                    <flux:text class="font-medium">{{ $user->name }}</flux:text>
                </div>

                <div>
                    <flux:text size="sm" class="text-zinc-500">{{ __('Email') }}</flux:text>
                    <flux:text class="font-medium">{{ $user->email }}</flux:text>
                </div>

                <div>
                    <flux:text size="sm" class="text-zinc-500">{{ __('Email verified') }}</flux:text>
                    <div class="mt-1">
                        @if ($user->email_verified_at)
                            <flux:badge color="green" size="sm">{{ $user->email_verified_at->format('Y-m-d H:i') }}</flux:badge>
                        @else
                            <flux:badge color="zinc" size="sm">{{ __('Unverified') }}</flux:badge>
                        @endif
                    </div>
                </div>

                <div>
                    <flux:text size="sm" class="text-zinc-500">{{ __('Created') }}</flux:text>
                    <flux:text class="font-medium">{{ $user->created_at?->format('Y-m-d H:i') }}</flux:text>
                </div>

                <div>
                    <flux:text size="sm" class="text-zinc-500">{{ __('Updated') }}</flux:text>
                    <flux:text class="font-medium">{{ $user->updated_at?->format('Y-m-d H:i') }}</flux:text>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <flux:button :href="route('users.show', $user)" wire:navigate variant="filled" icon="arrow-up-right">
                    {{ __('Open full page') }}
                </flux:button>

                <flux:modal.close>
                    <flux:button variant="primary" type="button">{{ __('Close') }}</flux:button>
                </flux:modal.close>
            </div>
        @endif
    </div>
</flux:modal>
