---
name: alpinejs
description: "Use for any task involving Alpine.js — adding interactivity, toggling visibility, handling events, binding data, animations, or client-side state. Activate when the user mentions x-data, x-on, x-show, x-model, x-bind, x-transition, $dispatch, $watch, or any other Alpine directive or magic. Also activate when the user wants lightweight JS behavior in a Blade or Livewire template without reaching for a full JS framework. Do not use for pure PHP/Livewire server-side logic, or for Vue/React/Inertia work."
license: MIT
metadata:
  author: laravel
---

# Alpine.js Development

Alpine.js is already bundled with Livewire 4 — **do not add a separate Alpine script tag** in Livewire projects. It is available globally in all Blade templates.

## Documentation

Local docs are at `code-samples/alpinejs/alpinejs-doc.md` — use this as the table of contents to locate the right topic file (e.g. `code-samples/alpinejs/directives/transition.md`). Read the specific file before writing non-trivial Alpine code. Use `search-docs` for anything not covered locally.

## Directive Reference

| Directive | Purpose |
|-----------|---------|
| `x-data` | Declares a component scope and its reactive data |
| `x-init` | Runs an expression when the component initialises |
| `x-show` | Toggles `display` based on a boolean expression |
| `x-if` | Conditionally adds/removes an element from the DOM (use inside `<template>`) |
| `x-bind` | Dynamically binds attributes (shorthand: `:attr`) |
| `x-on` | Listens for DOM events (shorthand: `@event`) |
| `x-model` | Two-way binds an input to a data property |
| `x-modelable` | Exposes an internal property for `x-model` from a parent |
| `x-text` | Sets element text content |
| `x-html` | Sets element inner HTML (use with caution — XSS risk) |
| `x-for` | Renders a list (use inside `<template>`) |
| `x-transition` | Applies enter/leave CSS transitions |
| `x-effect` | Re-runs an expression reactively when its dependencies change |
| `x-ref` | Gives an element a named reference via `$refs` |
| `x-cloak` | Hides element until Alpine initialises (remove flash of template) |
| `x-teleport` | Renders element elsewhere in the DOM |
| `x-ignore` | Prevents Alpine from initialising a subtree |
| `x-id` | Scopes auto-generated IDs for accessibility |

## Magic Reference

| Magic | Purpose |
|-------|---------|
| `$el` | The current root DOM element |
| `$refs` | Access elements marked with `x-ref` |
| `$watch` | Watch a data property and run a callback on change |
| `$watchEffect` | Runs a callback reactively (tracks its own dependencies) |
| `$dispatch` | Dispatches a custom DOM event |
| `$nextTick` | Defers a callback until after Alpine's next DOM update |
| `$id` | Generates a scoped unique ID (pairs with `x-id`) |

## Common Patterns

### Toggle

```html
<div x-data="{ open: false }">
    <button @click="open = !open">Toggle</button>
    <div x-show="open">Content</div>
</div>
```

### Dropdown with click-outside close

```html
<div x-data="{ open: false }" @click.outside="open = false">
    <button @click="open = !open">Menu</button>
    <ul x-show="open" x-transition>
        <li><a href="#">Item 1</a></li>
        <li><a href="#">Item 2</a></li>
    </ul>
</div>
```

### Transition

```html
<div x-show="open"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 scale-95"
     x-transition:enter-end="opacity-100 scale-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-95">
    Content
</div>
```

### Two-way input binding

```html
<div x-data="{ name: '' }">
    <input x-model="name" placeholder="Your name" />
    <p>Hello, <span x-text="name"></span></p>
</div>
```

### Reactive list

```html
<div x-data="{ items: ['Apples', 'Bananas', 'Cherries'] }">
    <template x-for="item in items" :key="item">
        <li x-text="item"></li>
    </template>
</div>
```

### Watch a value

```html
<div x-data="{ count: 0 }" x-init="$watch('count', val => console.log('count is', val))">
    <button @click="count++">Increment</button>
</div>
```

### Dispatching and listening to events

```html
<!-- Dispatcher -->
<div x-data>
    <button @click="$dispatch('notify', { message: 'Saved!' })">Save</button>
</div>

<!-- Listener (anywhere in the DOM) -->
<div x-data="{ message: '' }" @notify.window="message = $event.detail.message">
    <p x-text="message"></p>
</div>
```

### Reusable component with Alpine.data()

```html
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('dropdown', () => ({
        open: false,
        toggle() { this.open = !this.open },
    }))
})
</script>

<div x-data="dropdown">
    <button @click="toggle">Open</button>
    <div x-show="open">...</div>
</div>
```

## Livewire Integration

Alpine is bundled in Livewire 4 — use `$wire` to call Livewire actions or read properties from Alpine:

```html
<!-- Call a Livewire action from Alpine -->
<button @click="$wire.save()">Save</button>

<!-- Read a Livewire property -->
<span x-text="$wire.count"></span>

<!-- Open a Flux modal from Alpine (Flux-specific) -->
<button @click="$flux.modal('confirm').show()">Delete</button>
```

Use `@entangle` to two-way bind an Alpine property to a Livewire property:

```html
<div x-data="{ open: $wire.entangle('showModal') }">
    <div x-show="open">Modal content</div>
</div>
```

## Verification

1. Check browser console for Alpine init errors.
2. Ensure no separate `<script src="alpinejs">` tag exists in Livewire projects — it's already bundled.
3. `x-for` and `x-if` must be on a `<template>` element, not a real element.
4. Event listeners with `.window` or `.document` modifiers listen globally — remove them when the component is destroyed to avoid leaks.

## Common Pitfalls

- Using `x-if` or `x-for` on a non-`<template>` element — they only work on `<template>`.
- Forgetting `x-cloak` + corresponding CSS (`[x-cloak] { display: none; }`) causes a flash of un-initialised template on page load.
- Manually importing Alpine in a Livewire project — it's already bundled; a duplicate import breaks reactivity.
- Using `x-html` with user-generated content — always sanitise first to prevent XSS.
- Modifying reactive data inside `x-init` before Alpine has finished booting — use `$nextTick` if you need post-render access.
- Dispatching events to Livewire with `$dispatch` when `$wire.dispatch()` or `$wire.call()` is the correct API.
