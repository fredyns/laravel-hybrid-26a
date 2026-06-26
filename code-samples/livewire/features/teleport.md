# Teleport

**Source URL:** https://livewire.laravel.com/docs/4.x/teleport

## Overview

Livewire allows you to teleport part of your template to another part of the DOM on the page entirely.

This is useful for things like nested dialogs. When nesting one dialog inside of another, the z-index of the parent modal is applied to the nested modal. This can cause problems with styling backdrops and overlays. To avoid this problem, you can use Livewire's `@teleport` directive to render each nested modal as siblings in the rendered DOM.

This functionality is powered by [Alpine's x-teleport directive](https://alpinejs.dev/directives/teleport).

## Basic Usage

To teleport a portion of your template to another part of the DOM, you can wrap it in Livewire's `@teleport` directive.

Below is an example of using `@teleport` to render a modal dialog's contents at the end of the `<body>` element on the page:

```html
<div>
    <!-- Modal -->
    <div x-data="{ open: false }">
        <button @click="open = ! open">Toggle Modal</button>

        @teleport('body')
            <div x-show="open">
                Modal contents...
            </div>
        @endteleport
    </div>
</div>
```

The `@teleport` selector can be any string you would normally pass into something like `document.querySelector()`.

You can learn more about `document.querySelector()` by consulting its [MDN documentation](https://developer.mozilla.org/en-US/docs/Web/API/Document/querySelector).

Now, when the above Livewire template is rendered on the page, the contents portion of the modal will be rendered at the end of `<body>`:

```html
<body>
    <!-- ... -->

    <div x-show="open">
        Modal contents...
    </div>
</body>
```

## Important Constraints

### You must teleport outside the component

Livewire only supports teleporting HTML outside your components. For example, teleporting a modal to the `<body>` tag is fine, but teleporting it to another element within your component will not work.

### Teleporting only works with a single root element

Make sure you only include a single root element inside your `@teleport` statement.

## Common Use Cases

### Nested Modals

Solve z-index issues with nested modals by teleporting them to the same level:

```html
<div x-data="{ open: false }">
    <button @click="open = !open">Open Modal</button>

    @teleport('body')
        <div x-show="open" class="fixed inset-0 bg-black bg-opacity-50">
            <div class="bg-white rounded-lg p-6">
                <h2>Modal Title</h2>
                <p>Modal content goes here</p>
                <button @click="open = false">Close</button>
            </div>
        </div>
    @endteleport
</div>
```

### Notifications

Teleport notifications to a dedicated container:

```html
@teleport('#notifications')
    <div class="alert alert-success">
        {{ $message }}
    </div>
@endteleport
```

### Dropdowns

Prevent dropdown overflow by teleporting to body:

```html
<div x-data="{ open: false }" class="relative">
    <button @click="open = !open">Menu</button>

    @teleport('body')
        <div x-show="open" class="absolute top-full left-0 bg-white shadow-lg">
            <a href="/profile">Profile</a>
            <a href="/settings">Settings</a>
            <a href="/logout">Logout</a>
        </div>
    @endteleport
</div>
```

## Setting Up Teleport Containers

In your main layout, create containers for teleported content:

```html
<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html>
    <head>
        <!-- ... -->
    </head>
    <body>
        {{ $slot }}

        <!-- Teleport containers -->
        <div id="modals"></div>
        <div id="notifications"></div>
        <div id="dropdowns"></div>
    </body>
</html>
```

## Teleporting with Livewire Components

You can teleport content from nested Livewire components:

```html
<!-- Parent component -->
<div>
    <h1>Parent Component</h1>
    <livewire:child-component />
</div>

<!-- Child component -->
<div>
    <button wire:click="showModal">Show Modal</button>

    @if ($showModal)
        @teleport('body')
            <div class="modal">
                <h2>Modal from Child Component</h2>
                <button wire:click="hideModal">Close</button>
            </div>
        @endteleport
    @endif
</div>
```

## Combining with Alpine

Teleport works seamlessly with Alpine directives for animations and interactions:

```html
@teleport('body')
    <div
        x-show="open"
        x-transition
        class="fixed inset-0 bg-black bg-opacity-50"
    >
        <div
            x-transition
            class="bg-white rounded-lg p-6 max-w-md mx-auto mt-20"
        >
            <h2>Animated Modal</h2>
            <p>This modal has smooth transitions</p>
        </div>
    </div>
@endteleport
```

## See Also

- [Blade Directives - @teleport](../blade-directives/teleport.md) — Teleport directive documentation
- [Alpine](./alpine.md) — Use Alpine for client-side interactions
- [Components](../essentials/components.md) — Learn about Livewire components
