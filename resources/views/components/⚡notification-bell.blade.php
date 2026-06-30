<?php

use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    #[Computed]
    public function unreadCount(): int
    {
        return auth()->user()->unreadNotifications()->count();
    }

    /**
     * @return Collection<int, \Illuminate\Notifications\DatabaseNotification>
     */
    #[Computed]
    public function recent(): Collection
    {
        return auth()->user()->notifications()->latest()->limit(10)->get();
    }

    /**
     * Refresh when a realtime notification arrives (dispatched from app.js).
     */
    #[On('notification-received')]
    public function refresh(): void
    {
        unset($this->unreadCount, $this->recent);
    }

    public function markAllRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();

        unset($this->unreadCount, $this->recent);
    }
};
?>

<flux:dropdown position="bottom" align="end">
    <flux:button variant="subtle" size="sm" square aria-label="{{ __('Notifications') }}" class="relative">
        <flux:icon.bell variant="mini" />
        @if ($this->unreadCount > 0)
            <flux:badge size="sm" color="red" class="absolute -top-1 -end-1">{{ $this->unreadCount }}</flux:badge>
        @endif
    </flux:button>

    <flux:menu class="w-80">
        <div class="flex items-center justify-between px-2 py-1.5">
            <flux:heading size="sm">{{ __('Notifications') }}</flux:heading>
            @if ($this->unreadCount > 0)
                <flux:button variant="ghost" size="xs" wire:click="markAllRead">{{ __('Mark all read') }}</flux:button>
            @endif
        </div>

        <flux:menu.separator />

        @forelse ($this->recent as $notification)
            <flux:menu.item
                :href="$notification->data['url'] ?? '#'"
                wire:navigate
                class="{{ $notification->read_at ? '' : 'font-semibold' }}"
            >
                <div class="flex flex-col">
                    <span>{{ $notification->data['title'] ?? __('Notification') }}</span>
                    <flux:text size="xs" class="opacity-70">{{ $notification->data['body'] ?? '' }}</flux:text>
                </div>
            </flux:menu.item>
        @empty
            <div class="px-3 py-6 text-center">
                <flux:text size="sm" class="opacity-70">{{ __('No notifications yet') }}</flux:text>
            </div>
        @endforelse

        <flux:menu.separator />

        <flux:menu.item icon="arrow-right" :href="route('notifications.index')" wire:navigate>
            {{ __('View all notifications') }}
        </flux:menu.item>
    </flux:menu>
</flux:dropdown>
