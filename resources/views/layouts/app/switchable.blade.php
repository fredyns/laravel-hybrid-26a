<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        @php
            $layoutMode = request()->cookie('layout_mode', 'sidebar');

            $route = Route::currentRouteName();
            $crumbs = [];

            if (str_starts_with($route, 'users.')) {
                $crumbs[] = ['label' => __('Users'), 'href' => route('users.index')];
                if ($route === 'users.show') {
                    $user = Route::current()->parameter('user');
                    $crumbs[] = ['label' => $user?->name ?? __('User')];
                }
            } elseif (str_starts_with($route, 'roles.')) {
                $crumbs[] = ['label' => __('Roles'), 'href' => route('roles.index')];
                if ($route === 'roles.show') {
                    $role = Route::current()->parameter('role');
                    $crumbs[] = ['label' => $role?->name ?? __('Role')];
                }
            } elseif (in_array($route, ['profile.edit', 'appearance.edit', 'security.edit'])) {
                $crumbs[] = ['label' => __('Settings')];
            }

            $isLastCrumb = fn (int $i) => $i === count($crumbs) - 1;
        @endphp

        @if ($layoutMode === 'header')
            {{-- ==================== HEADER MODE ==================== --}}
            <flux:header container sticky class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
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

                    <flux:button variant="subtle" icon="bell" size="sm" square aria-label="{{ __('Notifications') }}" />

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

            {{-- Mobile sidebar for header mode --}}
            <flux:sidebar sticky collapsible="mobile" class="lg:hidden border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
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
                </flux:sidebar.nav>
            </flux:sidebar>
        @else
            {{-- ==================== SIDEBAR MODE ==================== --}}
            <flux:sidebar sticky collapsible class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:sidebar.header>
                    <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                </flux:sidebar.header>

                <flux:sidebar.nav>
                    <div class="px-3 py-2 in-data-flux-sidebar-collapsed-desktop:hidden">
                        <div class="text-sm text-zinc-400 font-medium leading-none">{{ __('Platform') }}</div>
                    </div>
                    <flux:separator class="hidden in-data-flux-sidebar-collapsed-desktop:block mx-auto my-1" />

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
                </flux:sidebar.nav>

                <flux:spacer />

                <flux:sidebar.nav>
                    <flux:sidebar.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                        {{ __('Repository') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                        {{ __('Documentation') }}
                    </flux:sidebar.item>
                </flux:sidebar.nav>

                <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
            </flux:sidebar>

            <flux:header class="border-b border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                <flux:sidebar.toggle icon="bars-2" inset="left" />

                <flux:breadcrumbs>
                    <flux:breadcrumbs.item :href="route('dashboard')" icon="home" />
                    @foreach ($crumbs as $i => $crumb)
                        @if (isset($crumb['href']) && !$isLastCrumb($i))
                            <flux:breadcrumbs.item :href="$crumb['href']">{{ $crumb['label'] }}</flux:breadcrumbs.item>
                        @else
                            <flux:breadcrumbs.item>{{ $crumb['label'] }}</flux:breadcrumbs.item>
                        @endif
                    @endforeach
                </flux:breadcrumbs>

                <flux:spacer />

                <div class="flex items-center gap-0.5">
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

                    <flux:button variant="subtle" icon="bell" size="sm" square aria-label="{{ __('Notifications') }}" />

                    <flux:separator vertical variant="subtle" class="mx-1 lg:hidden my-2" />

                    <flux:dropdown position="bottom" align="end" class="lg:hidden">
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
            </flux:header>
        @endif

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
