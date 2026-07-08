@props([
    'sidebar' => false,
])

@if($sidebar)
    <a {{ $attributes->merge(['class' => 'flex items-center gap-2 px-1']) }}>
        <div class="flex aspect-square size-8 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 shadow-sm shadow-blue-500/30">
            <x-app-logo-icon class="size-5 fill-current text-white" />
        </div>
        <span class="truncate text-sm font-semibold in-data-flux-sidebar-collapsed-desktop:hidden">
            {{ config('app.name', 'Laravel') }}
        </span>
    </a>
@else
    <flux:brand name="{{ config('app.name', 'Laravel') }}" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 shadow-sm shadow-blue-500/30">
            <x-app-logo-icon class="size-5 fill-current text-white" />
        </x-slot>
    </flux:brand>
@endif
