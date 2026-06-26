# wire:dirty

**Source URL:** https://livewire.laravel.com/docs/4.x/wire-dirty

## Overview

In a traditional HTML page containing a form, the form is only ever submitted when the user presses the "Submit" button.

However, Livewire is capable of much more than traditional form submissions. You can validate form inputs in real-time or even save the form as a user types.

In these "real-time" update scenarios, it can be helpful to signal to your users when a form or subset of a form has been changed, but hasn't been saved to the database.

When a form contains un-saved input, that form is considered "dirty". It only becomes "clean" when a network request has been triggered to synchronize the server state with the client-side state.

## Basic Usage

Livewire allows you to easily toggle visual elements on the page using the `wire:dirty` directive.

By adding `wire:dirty` to an element, you are instructing Livewire to only show the element when the client-side state diverges from the server-side state.

To demonstrate, here is an example of an UpdatePost form containing a visual "Unsaved changes..." indication that signals to the user that the form contains input that has not been saved:

```html
<form wire:submit="update">
    <input type="text" wire:model="title">

    <!-- ... -->

    <button type="submit">Update</button>

    <div wire:dirty>Unsaved changes...</div>
</form>
```

Because `wire:dirty` has been added to the "Unsaved changes..." message, the message will be hidden by default. Livewire will automatically display the message when the user starts modifying the form inputs.

When the user submits the form, the message will disappear again, since the server / client data is back in sync.

## Removing Elements

By adding the `.remove` modifier to `wire:dirty`, you can instead show an element by default and only hide it when the component has "dirty" state:

```html
<div wire:dirty.remove>The data is in-sync...</div>
```

## Targeting Property Updates

Imagine you are using `wire:model.live.blur` to update a property on the server immediately after a user leaves an input field. In this scenario, you can provide a "dirty" indication for only that property by adding `wire:target` to the element that contains the `wire:dirty` directive.

Here is an example of only showing a dirty indication when the title property has been changed:

```html
<form wire:submit="update">
    <input wire:model.live.blur="title">

    <div wire:dirty wire:target="title">Unsaved title...</div>

    <button type="submit">Update</button>
</form>
```

## Toggling Classes

Often, instead of toggling entire elements, you may want to toggle individual CSS classes on an input when its state is "dirty".

Below is an example where a user types into an input field and the border becomes yellow, indicating an "unsaved" state. Then, when the user tabs away from the field, the border is removed, indicating that the state has been saved on the server:

```html
<input wire:model.live.blur="title" wire:dirty.class="border-yellow-500">
```

## Using the $dirty Expression

In addition to the `wire:dirty` directive, you can check dirty state programmatically using the `$dirty` expression in Livewire directives or `$wire.$dirty()` in Alpine.

### Check if Entire Component is Dirty

To check if any property on the component has unsaved changes:

```html
<div wire:show="$dirty">You have unsaved changes</div>
```

### Check if a Specific Property is Dirty

To check if a specific property has been modified:

```html
<div wire:show="$dirty('title')">Title has been modified</div>
```

You can also check nested properties:

```html
<div wire:show="$dirty('user.name')">Name has been modified</div>
```

### Conditional Logic Based on Dirty State

You can use `$wire.$dirty()` in Alpine to conditionally run logic:

```html
<button x-on:click="$wire.$dirty('title') && $wire.save()">
    Save Title
</button>
```

Or apply conditional classes with Alpine:

```html
<input
    wire:model="email"
    :class="$wire.$dirty('email') && 'border-yellow-500'"
>
```

## Reference

**Syntax:**
- `wire:dirty`
- `wire:target="property"`

## Modifiers

| Modifier | Description |
|----------|-------------|
| `.remove` | Show element by default, hide when dirty |
| `.class="class-name"` | Add a CSS class when dirty |

## $dirty Expression

| Expression | Description |
|-----------|-------------|
| `$dirty` | Returns true if any property has unsaved changes |
| `$dirty('property')` | Returns true if the specified property has unsaved changes |
| `$dirty(['title', 'description'])` | Returns true if any of the specified properties have unsaved changes |

Can be used in Livewire directives like `wire:show="$dirty"` or in Alpine as `$wire.$dirty()`.

## See Also

- [Forms](../essentials/forms.md) — Handle form submissions
- [wire:model](./wire-model.md) — Two-way data binding
- [Validation](../features/validation.md) — Validate form inputs
