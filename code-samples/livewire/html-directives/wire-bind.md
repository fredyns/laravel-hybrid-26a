# wire:bind

**Source URL:** https://livewire.laravel.com/docs/4.x/wire-bind

## Overview

`wire:bind` is a directive that dynamically binds HTML attributes to component properties or expressions. Unlike using Blade's attribute syntax, `wire:bind` updates the attribute reactively on the client without requiring a full re-render.

If you are familiar with Alpine's `x-bind` directive, the two are essentially the same.

## Basic Usage

```html
<input wire:model="message" wire:bind:class="message.length > 240 && 'text-red-500'">
```

As the user types, `wire:bind:class` reacts to the message length and applies the class instantly on the client.

## Common Use Cases

### Binding Styles

```html
<div wire:bind:style="{ 'color': textColor, 'font-size': fontSize + 'px' }">
    Styled text
</div>
```

### Binding href

```html
<a wire:bind:href="url">Dynamic link</a>
```

### Binding disabled State

```html
<button wire:bind:disabled="isArchived">Delete</button>
```

### Binding Data Attributes

```html
<div wire:bind:data-count="count">...</div>
```

## Reference

**Syntax:** `wire:bind:{attribute}="expression"`

Replace `{attribute}` with any valid HTML attribute name (e.g., `class`, `style`, `href`, `disabled`, `data-*`).

This directive has no modifiers.

## See Also

- [Alpine](../features/alpine.md) — Client-side interactivity with Alpine.js
- [Properties](../essentials/properties.md) — Manage component state
