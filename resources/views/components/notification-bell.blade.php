@php
    $unreadCount = auth()->user()->unreadNotifications()->count();
    $recent = auth()->user()->notifications()->latest()->limit(10)->get();
@endphp

<flux:dropdown position="bottom" align="end">
    <flux:button
        variant="subtle"
        size="sm"
        square
        aria-label="{{ $unreadCount > 0 ? __(':count unread notifications', ['count' => $unreadCount]) : __('Notifications') }}"
        class="relative"
    >
        <flux:icon.bell variant="mini" />

        @if ($unreadCount > 0)
            {{-- Count lives in the button's aria-label, so the badge itself is decorative. --}}
            <span
                aria-hidden="true"
                class="absolute top-0 right-0 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-semibold leading-none text-white"
            >
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </flux:button>

    <flux:menu class="w-80">
        <div class="flex items-center justify-between px-2 py-1.5">
            <flux:heading size="sm">{{ __('Notifications') }}</flux:heading>
        </div>

        <flux:menu.separator />

        @forelse ($recent as $notification)
            <flux:menu.item
                :href="$notification->data['url'] ?? '#'"
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

        <flux:menu.item icon="arrow-right" :href="route('notifications.index')">
            {{ __('View all notifications') }}
        </flux:menu.item>
    </flux:menu>
</flux:dropdown>
