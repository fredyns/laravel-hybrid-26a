
{{-- ==================== HEADER MODE ==================== --}}
<div class="[grid-area:header] sticky top-0 z-10">
<flux:header container class="border-b border-zinc-200 bg-gradient-to-r from-blue-50 via-indigo-50 to-violet-50 dark:border-zinc-800 dark:from-zinc-900 dark:via-indigo-950/30 dark:to-zinc-900 ![grid-area:unset]">
    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

    <x-app-logo href="{{ route('dashboard') }}" wire:navigate class="max-lg:hidden" />

    <div class="lg:hidden flex-1 flex justify-center">
        <x-app-logo href="{{ route('dashboard') }}" wire:navigate />
    </div>

    <flux:navbar class="-mb-px max-lg:hidden">
        <flux:navbar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
            {{ __('Dashboard') }}
        </flux:navbar.item>

        @can('users.index')
            <flux:navbar.item icon="users" :href="route('users.index')" :current="request()->routeIs('users.*')" wire:navigate>
                {{ __('Users') }}
            </flux:navbar.item>
        @endcan

        @can('rbac.index')
            <flux:navbar.item icon="shield-check" :href="route('roles.index')" :current="request()->routeIs('roles.*')" wire:navigate>
                {{ __('Roles') }}
            </flux:navbar.item>
        @endcan

        <flux:navbar.item icon="bell" :href="route('notifications.index')" :current="request()->routeIs('notifications.*')" wire:navigate>
            {{ __('Notifications') }}
        </flux:navbar.item>
    </flux:navbar>

    <flux:spacer />

    {{-- Desktop right-side items --}}
    <div class="flex items-center gap-0.5 max-lg:hidden">
        <flux:dropdown x-data align="end">
            <flux:button variant="subtle" size="sm" square aria-label="{{ __('Color scheme') }}">
                <flux:icon.sun x-show="$flux.appearance === 'light'" variant="mini" class="text-zinc-500 dark:text-white" />
                <flux:icon.moon x-show="$flux.appearance === 'dark'" variant="mini" class="text-zinc-500 dark:text-white" />
                <flux:icon.moon x-show="$flux.appearance === 'system' && $flux.dark" variant="mini" />
                <flux:icon.sun x-show="$flux.appearance === 'system' && ! $flux.dark" variant="mini" />
            </flux:button>

            <flux:menu>
                <flux:menu.item icon="sun" x-on:click="$flux.appearance = 'light'">{{ __('Light') }}</flux:menu.item>
                <flux:menu.item icon="moon" x-on:click="$flux.appearance = 'dark'">{{ __('Dark') }}</flux:menu.item>
                <flux:menu.item icon="computer-desktop" x-on:click="$flux.appearance = 'system'">{{ __('System') }}</flux:menu.item>
            </flux:menu>
        </flux:dropdown>

        <livewire:notification-bell />

        <flux:separator vertical variant="subtle" class="mx-1 my-2" />

        <flux:dropdown position="bottom" align="end">
            <flux:profile
                :initials="auth()->user()->initials()"
                icon-trailing="chevron-down"
            />

            <flux:menu>
                <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                    <flux:avatar
                        :name="auth()->user()->name"
                        :initials="auth()->user()->initials()"
                    />
                    <div class="grid flex-1 text-start text-sm leading-tight">
                        <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                        <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                    </div>
                </div>

                <flux:menu.separator />

                <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                    {{ __('Settings') }}
                </flux:menu.item>

                <flux:menu.separator />

                <form method="POST" action="{{ route('layout-mode.toggle') }}" class="w-full">
                    @csrf
                    <input type="hidden" name="mode" value="sidebar">
                    <flux:menu.item as="button" type="submit" icon="view-columns" class="w-full cursor-pointer">
                        {{ __('Sidebar layout') }}
                    </flux:menu.item>
                </form>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item
                        as="button"
                        type="submit"
                        icon="arrow-right-start-on-rectangle"
                        class="w-full cursor-pointer"
                        data-test="logout-button"
                    >
                        {{ __('Log out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </div>

    {{-- Mobile right-side context menu --}}
    <flux:dropdown position="bottom" align="end" class="lg:hidden">
        <flux:button variant="subtle" icon="ellipsis-vertical" size="sm" square aria-label="{{ __('Menu') }}" />

        <flux:menu>
            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                <flux:avatar
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                />
                <div class="grid flex-1 text-start text-sm leading-tight">
                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                </div>
            </div>

            <flux:menu.separator />

            <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                {{ __('Settings') }}
            </flux:menu.item>

            <flux:menu.separator />

            <flux:menu.item x-data icon="sun" x-on:click="$flux.appearance = $flux.dark ? 'light' : 'dark'">
                {{ __('Toggle dark mode') }}
            </flux:menu.item>

            <flux:menu.separator />

            <form method="POST" action="{{ route('layout-mode.toggle') }}" class="w-full">
                @csrf
                <input type="hidden" name="mode" value="sidebar">
                <flux:menu.item as="button" type="submit" icon="view-columns" class="w-full cursor-pointer">
                    {{ __('Sidebar layout') }}
                </flux:menu.item>
            </form>

            <flux:menu.separator />

            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <flux:menu.item
                    as="button"
                    type="submit"
                    icon="arrow-right-start-on-rectangle"
                    class="w-full cursor-pointer"
                    data-test="logout-button"
                >
                    {{ __('Log out') }}
                </flux:menu.item>
            </form>
        </flux:menu>
    </flux:dropdown>
</flux:header>
</div>

{{-- Breadcrumbs --}}
@if (count($crumbs) > 0)
    <div class="[grid-area:header] bg-white dark:bg-zinc-800 mt-12 pt-2">
        <div class="mx-auto max-w-7xl px-6 lg:px-8 py-1.5 opacity-80">
            <flux:breadcrumbs class="text-xs">
                <flux:breadcrumbs.item :href="route('dashboard')" icon="home" />
                @foreach ($crumbs as $i => $crumb)
                    @if (isset($crumb['href']) && !$isLastCrumb($i))
                        <flux:breadcrumbs.item :href="$crumb['href']">{{ $crumb['label'] }}</flux:breadcrumbs.item>
                    @else
                        <flux:breadcrumbs.item>{{ $crumb['label'] }}</flux:breadcrumbs.item>
                    @endif
                @endforeach
            </flux:breadcrumbs>
        </div>
    </div>
@endif

{{-- Mobile sidebar for header mode --}}
<flux:sidebar sticky collapsible="mobile" class="lg:hidden border-e border-zinc-200 bg-gradient-to-b from-blue-50 via-indigo-50 to-violet-50 dark:border-zinc-800 dark:from-zinc-900 dark:via-indigo-950/30 dark:to-zinc-900">
    <flux:sidebar.header>
        <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
        <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
    </flux:sidebar.header>

    <flux:sidebar.nav>
        <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
            {{ __('Dashboard') }}
        </flux:sidebar.item>

        @can('users.index')
            <flux:sidebar.item icon="users" :href="route('users.index')" :current="request()->routeIs('users.*')" wire:navigate>
                {{ __('Users') }}
            </flux:sidebar.item>
        @endcan

        @can('rbac.index')
            <flux:sidebar.item icon="shield-check" :href="route('roles.index')" :current="request()->routeIs('roles.*')" wire:navigate>
                {{ __('Roles') }}
            </flux:sidebar.item>
        @endcan

        <flux:sidebar.item icon="bell" :href="route('notifications.index')" :current="request()->routeIs('notifications.*')" wire:navigate>
            {{ __('Notifications') }}
        </flux:sidebar.item>
    </flux:sidebar.nav>
</flux:sidebar>
