# wire:confirm

**Source URL:** https://livewire.laravel.com/docs/4.x/wire-confirm

## Overview

Before performing dangerous actions in Livewire, you may want to provide your users with some sort of visual confirmation.

Livewire makes this easy to do by adding `wire:confirm` in addition to any action (`wire:click`, `wire:submit`, etc.).

## Basic Usage

Here's an example of adding a confirmation dialog to a "Delete post" button:

```html
<button
    type="button"
    wire:click="delete"
    wire:confirm="Are you sure you want to delete this post?"
>
    Delete post
</button>
```

When a user clicks "Delete post", Livewire will trigger a confirmation dialog (The default browser confirmation alert). If the user hits escape or presses cancel, the action won't be performed. If they press "OK", the action will be completed.

## Prompting Users for Input

For even more dangerous actions such as deleting a user's account entirely, you may want to present them with a confirmation prompt which they would need to type in a specific string of characters to confirm the action.

Livewire provides a helpful `.prompt` modifier, that when applied to `wire:confirm`, it will prompt the user for input and only confirm the action if the input matches (case-sensitive) the provided string (designated by a `|` (pipe) character at the end of the `wire:confirm` value):

```html
<button
    type="button"
    wire:click="delete"
    wire:confirm.prompt="Are you sure?\n\nType DELETE to confirm|DELETE"
>
    Delete account
</button>
```

When a user presses "Delete account", the action will only be performed if "DELETE" is entered into the prompt, otherwise, the action will be cancelled.

## Reference

**Syntax:** `wire:confirm="message"`

## Modifiers

| Modifier | Description |
|----------|-------------|
| `.prompt` | Prompt user for input; format: "message\|expected-input" |

## See Also

- [wire:click](./wire-click.md) — Trigger actions on click
- [wire:submit](./wire-submit.md) — Handle form submissions
- [Actions](../essentials/actions.md) — Handle component methods
