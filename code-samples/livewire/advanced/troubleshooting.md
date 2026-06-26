# Troubleshooting

**Source URL:** https://livewire.laravel.com/docs/4.x/troubleshooting

## Overview

This guide covers common errors and scenarios you may encounter in your Livewire applications.

## Component Mismatches

When interacting with Livewire components, you may encounter error messages like:

- `Error: Component already initialized`
- `Error: Snapshot missing on Livewire component with id: ...`

The most common cause is forgetting to add `wire:key` to elements and components inside a `@foreach` loop.

### Adding wire:key

Any time you have a loop in your Blade templates, add `wire:key` to the opening tag of the first element within the loop:

```html
@foreach($posts as $post)
    <div wire:key="{{ $post->id }}">
        ...
    </div>
@endforeach
```

The same applies to Livewire components within a loop:

```html
@foreach($posts as $post)
    <livewire:show-post :$post :wire:key="$post->id" />
@endforeach
```

**Important:** When you have a Livewire component deeply nested inside a `@foreach` loop, you STILL need to add a key to it:

```html
@foreach($posts as $post)
    <div wire:key="{{ $post->id }}">
        ...
        <livewire:show-post :$post :wire:key="$post->id" />
        ...
    </div>
@endforeach
```

### Prefixing Keys

Another scenario is having duplicate keys within the same component. Add prefixes to designate each set of keys as unique:

```html
<div>
    @foreach($posts as $post)
        <div wire:key="post-{{ $post->id }}">...</div>
    @endforeach

    @foreach($authors as $author)
        <div wire:key="author-{{ $author->id }}">...</div>
    @endforeach
</div>
```

## Multiple Instances of Alpine

You may encounter error messages like:

- `Error: Detected multiple instances of Alpine running`
- `Alpine Expression Error: $wire is not defined`

This means you have two versions of Alpine running on the same page. Livewire includes its own bundle of Alpine, so you must remove any other versions of Alpine on Livewire pages.

### Removing Laravel Breeze's Alpine

If installing Livewire inside an existing Laravel Breeze (Blade + Alpine version), remove these lines from `resources/js/app.js`:

```javascript
import './bootstrap';

- import Alpine from 'alpinejs';
- 
- window.Alpine = Alpine;
- 
- Alpine.start();
```

### Removing a CDN Version of Alpine

If you included an Alpine CDN as a script tag in your layout, remove it. Livewire v3 automatically provides Alpine:

```html
     ...
-    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
 </head>
```

You can also remove any additional Alpine plugins, as Livewire includes all Alpine plugins except `@alpinejs/ui`.

### Missing @alpinejs/ui

Livewire's bundled Alpine includes all plugins EXCEPT `@alpinejs/ui`. If using Alpine Components, which relies on this plugin, you may encounter:

```
Uncaught Alpine: no element provided to x-anchor
```

Include the `@alpinejs/ui` plugin as a CDN:

```html
     ...
+    <script defer src="https://unpkg.com/@alpinejs/ui@3.13.7-beta.0/dist/cdn.min.js"></script>
 </head>
```

## See Also

- [Components](../essentials/components.md) — Learn about Livewire components
- [Alpine](../features/alpine.md) — Alpine.js integration
