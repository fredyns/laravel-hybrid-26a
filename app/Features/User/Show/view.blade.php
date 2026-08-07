<x-layouts::app :title="$user->name">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('users.index') }}">
                <flux:button variant="ghost" icon="arrow-left" size="sm" />
            </a>
            <div>
                <flux:heading size="xl">{{ $user->name }}</flux:heading>
                <flux:subheading>{{ $user->email }}</flux:subheading>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @can('users.update')
                <a href="{{ route('users.edit', $user) }}">
                    <flux:button variant="primary" icon="pencil-square">
                        {{ __('Edit') }}
                    </flux:button>
                </a>
            @endcan

            @can('users.change-password')
                <span class="cursor-not-allowed opacity-50">
                    <flux:button icon="key">
                        {{ __('Change password') }}
                    </flux:button>
                </span>
            @endcan

            @can('users.delete')
                <span class="cursor-not-allowed opacity-50">
                    <flux:button variant="danger" icon="trash">
                        {{ __('Delete') }}
                    </flux:button>
                </span>
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
    </div>
</x-layouts::app>
