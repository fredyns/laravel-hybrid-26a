# wire:navigate

**Source URL:** https://livewire.laravel.com/docs/4.x/wire-navigate

## Overview

Livewire's `wire:navigate` feature makes page navigation much faster, providing an SPA-like experience for your users.

Below is a simple example of adding `wire:navigate` to links in a nav bar:

```html
<nav>
    <a href="/" wire:navigate>Dashboard</a>
    <a href="/posts" wire:navigate>Posts</a>
    <a href="/users" wire:navigate>Users</a>
</nav>
```

When any of these links are clicked, Livewire will intercept the click and, instead of allowing the browser to perform a full page visit, Livewire will fetch the page in the background and swap it with the current page (resulting in much faster and smoother page navigation).

## Styling Active Links with data-current

Livewire automatically adds a `data-current` attribute to any `wire:navigate` link that matches the current page URL. This allows you to style active navigation links using CSS or Tailwind without any additional directives:

```html
<nav>
    <a href="/" wire:navigate class="data-current:font-bold">Dashboard</a>
    <a href="/posts" wire:navigate class="data-current:font-bold">Posts</a>
    <a href="/users" wire:navigate class="data-current:font-bold">Users</a>
</nav>
```

The `data-current` attribute is added and removed automatically as users navigate between pages.

## Prefetching Pages on Hover

By adding the `.hover` modifier, Livewire will pre-fetch a page when a user hovers over a link. This way, the page will have already been downloaded from the server when the user clicks on the link:

```html
<a href="/" wire:navigate.hover>Dashboard</a>
```

## Reference

**Syntax:** `wire:navigate`

## Modifiers

| Modifier | Description |
|----------|-------------|
| `.hover` | Prefetches the page when user hovers over the link |

## See Also

- [Navigate](../features/navigate.md) — Complete guide to SPA navigation
- [Pages](../essentials/pages.md) — Create routable page components
- [@persist](../blade-directives/persist.md) — Persist elements during navigation
