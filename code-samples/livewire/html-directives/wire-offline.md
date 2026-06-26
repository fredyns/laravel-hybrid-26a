# wire:offline

**Source URL:** https://livewire.laravel.com/docs/4.x/wire-offline

## Overview

In real-time applications, it can be helpful to provide a visual indication that the user's device is no longer connected to the internet.

For example, if you have built a blogging platform on Livewire, you may want to notify your users if they are offline so that they don't draft an entire blog post without the ability for Livewire to save it to the database.

Livewire provides the `wire:offline` directive for such cases. By adding `wire:offline` to an element inside a Livewire component, it will be hidden by default and become visible when the user loses connection:

```html
<div wire:offline>
    This device is currently offline.
</div>
```

The element will disappear again when the network connection is restored.

## Toggling Classes

Adding the class modifier allows you to add a class to an element when the user loses their connection. The class will be removed again, once the user is back online:

```html
<div wire:offline.class="bg-red-300">
```

Or, using the `.remove` modifier, you can remove a class when a user loses their connection. In this example, the `bg-green-300` class will be removed from the `<div>` while the user has lost their connection:

```html
<div class="bg-green-300" wire:offline.class.remove="bg-green-300">
```

## Toggling Attributes

The `.attr` modifier allows you to add an attribute to an element when the user loses their connection. In this example, the "Save" button will be disabled while the user has lost their connection:

```html
<button wire:offline.attr="disabled">Save</button>
```

## Reference

**Syntax:** `wire:offline`

## Modifiers

| Modifier | Description |
|----------|-------------|
| `.class="class-name"` | Add a CSS class when offline |
| `.class.remove="class-name"` | Remove a CSS class when offline |
| `.attr="attribute"` | Add an HTML attribute when offline |

## See Also

- [wire:loading](./wire-loading.md) — Show loading states
- [Actions](../essentials/actions.md) — Handle component methods
