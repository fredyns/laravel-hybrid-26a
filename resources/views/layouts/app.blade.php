<x-layouts::app.switchable :title="$title ?? null">
    <flux:main :container="request()->cookie('layout_mode') === 'header'">
        {{ $slot }}
    </flux:main>
</x-layouts::app.switchable>
