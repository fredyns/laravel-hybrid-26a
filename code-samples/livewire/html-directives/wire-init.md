# wire:init

**Source URL:** https://livewire.laravel.com/docs/4.x/wire-init

## Overview

Livewire offers a `wire:init` directive to run an action as soon as the component is rendered. This can be helpful in cases where you don't want to hold up the entire page load, but want to load some data immediately after the page load.

## Basic Usage

```html
<div wire:init="loadPosts">
    <!-- ... -->
</div>
```

The `loadPosts` action will be run immediately after the Livewire component renders on the page.

**Note:** In most cases, Livewire's lazy loading feature is preferable to using `wire:init`.

## Reference

**Syntax:** `wire:init="action"`

This directive has no modifiers.

## See Also

- [Lazy Loading](../features/lazy.md) — Defer component rendering
- [Actions](../essentials/actions.md) — Handle component methods
- [wire:intersect](./wire-intersect.md) — Trigger actions when visible
