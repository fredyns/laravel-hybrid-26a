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
            @include('layouts.app.switchable-top')
        @else
            @include('layouts.app.switchable-side')
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
