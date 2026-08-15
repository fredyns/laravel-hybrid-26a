<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ __('Notifications') }}</flux:heading>
            <flux:subheading>{{ __('View and manage your notifications.') }}</flux:subheading>
        </div>

        <div class="flex items-center gap-2">
            <flux:button icon="bell-alert" wire:click="simulate" data-test="simulate-notification-button">
                {{ __('Simulate notification') }}
            </flux:button>

            @if ($unreadCount > 0)
                <flux:button variant="primary" icon="check" wire:click="markAllRead" data-test="mark-all-read-button">
                    {{ __('Mark all read') }}
                </flux:button>
            @endif
        </div>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('Status') }}</flux:table.column>
            <flux:table.column>{{ __('Notification') }}</flux:table.column>
            <flux:table.column>{{ __('Time') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Actions') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($notifications as $notification)
                <flux:table.row :key="$notification->id" wire:key="notification-{{ $notification->id }}">
                    <flux:table.cell>
                        @if ($notification->read_at)
                            <flux:badge size="sm" color="zinc" inset="top bottom">{{ __('Read') }}</flux:badge>
                        @else
                            <flux:badge size="sm" color="blue" inset="top bottom">{{ __('Unread') }}</flux:badge>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="flex flex-col gap-0.5">
                            <div class="flex items-center gap-2">
                                <span class="{{ $notification->read_at ? '' : 'font-semibold' }}">
                                    {{ $notification->data['title'] ?? __('Notification') }}
                                </span>
                                @if (isset($notification->data['variant']))
                                    <flux:badge size="sm" color="{{ match($notification->data['variant']) { 'success' => 'green', 'warning' => 'amber', 'danger' => 'red', 'info' => 'blue', default => 'zinc' } }}" inset="top bottom">
                                        {{ $notification->data['variant'] }}
                                    </flux:badge>
                                @endif
                            </div>
                            <flux:text size="sm" class="opacity-70">{{ $notification->data['body'] ?? '' }}</flux:text>
                        </div>
                    </flux:table.cell>

                    <flux:table.cell class="whitespace-nowrap">
                        <flux:text size="sm">{{ $notification->created_at->diffForHumans() }}</flux:text>
                    </flux:table.cell>

                    <flux:table.cell align="end">
                        <div class="flex justify-end gap-1">
                            @if (isset($notification->data['url']))
                                <flux:tooltip :content="__('View')">
                                    <a href="{{ $notification->data['url'] }}">
                                        <flux:button size="sm" variant="ghost" icon="eye" :aria-label="__('View')" />
                                    </a>
                                </flux:tooltip>
                            @endif

                            @unless ($notification->read_at)
                                <flux:tooltip :content="__('Mark as read')">
                                    <flux:button
                                        size="sm"
                                        variant="ghost"
                                        icon="check"
                                        :aria-label="__('Mark as read')"
                                        wire:click="markAsRead('{{ $notification->id }}')"
                                    />
                                </flux:tooltip>
                            @endunless

                            <flux:tooltip :content="__('Delete')">
                                <flux:button
                                    size="sm"
                                    variant="ghost"
                                    icon="trash"
                                    :aria-label="__('Delete')"
                                    wire:click="delete('{{ $notification->id }}')"
                                    wire:confirm="{{ __('Delete this notification?') }}"
                                />
                            </flux:tooltip>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="4">
                        <div class="py-12 text-center">
                            <flux:icon.bell class="mx-auto size-8 opacity-30" />
                            <flux:text class="mt-2">{{ __('No notifications yet.') }}</flux:text>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    {{ $notifications->links() }}
</div>
