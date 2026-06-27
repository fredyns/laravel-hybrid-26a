<?php

use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('User')] class extends Component {
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

    #[On('users-updated')]
    public function refreshUser(): void
    {
        $this->user->refresh();
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <flux:button :href="route('users.index')" wire:navigate variant="ghost" icon="arrow-left" size="sm" />
            <div>
                <flux:heading size="xl">{{ $user->name }}</flux:heading>
                <flux:subheading>{{ $user->email }}</flux:subheading>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @can('users.update')
                <flux:button wire:click="edit" variant="primary" icon="pencil-square">
                    {{ __('Edit') }}
                </flux:button>
            @endcan

            @can('users.change-password')
                <flux:button wire:click="changePassword" icon="key">
                    {{ __('Change password') }}
                </flux:button>
            @endcan
        </div>
    </div>

    <flux:separator />

    <div class="grid max-w-2xl grid-cols-1 gap-6 sm:grid-cols-2">
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

    <livewire:pages::users.form />
    <livewire:pages::users.change-password />
</div>
