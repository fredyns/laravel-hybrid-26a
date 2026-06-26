# Morphing

**Source URL:** https://livewire.laravel.com/docs/4.x/morphing

## Overview

When a Livewire component updates the browser's DOM, it does so in an intelligent way called "morphing". Instead of replacing a component's HTML with newly rendered HTML every time a component is updated, Livewire dynamically compares the current HTML with the new HTML, identifies differences, and makes surgical changes only where needed.

This preserves existing, unchanged elements on a component. Event listeners, focus state, and form input values are all preserved between Livewire updates. Morphing also offers increased performance compared to wiping and re-rendering new DOM on every update.

## How Morphing Works

To understand how Livewire determines which elements to update between requests, consider this simple Todos component:

```php
class Todos extends Component
{
    public $todo = '';

    public $todos = [
        'first',
        'second',
    ];

    public function add()
    {
        $this->todos[] = $this->todo;
    }
}
```

```html
<form wire:submit="add">
    <ul>
        @foreach ($todos as $item)
            <li wire:key="{{ $loop->index }}">{{ $item }}</li>
        @endforeach
    </ul>

    <input wire:model="todo">
</form>
```

The initial render outputs:

```html
<form wire:submit="add">
    <ul>
        <li>first</li>

        <li>second</li>
    </ul>

    <input wire:model="todo">
</form>
```

After typing "third" and pressing Enter, the newly rendered HTML includes:

```html
<form wire:submit="add">
    <ul>
        <li>first</li>

        <li>second</li>

        <li>third</li>
    </ul>

    <input wire:model="todo">
</form>
```

Livewire walks both HTML trees simultaneously, comparing elements for changes, additions, and removals, making surgical changes where needed.

## Morphing Shortcomings

### Inserting Intermediate Elements

Consider a CreatePost component with conditional error messages:

```html
<form wire:submit="save">
    <div>
        <input wire:model="title">
    </div>

    @if ($errors->has('title'))
        <div>{{ $errors->first('title') }}</div>
    @endif

    <div>
        <button>Save</button>
    </div>
</form>
```

When validation fails and an error message appears, Livewire may incorrectly morph the elements because it doesn't know whether to insert a new element or modify an existing one.

**Impacts of morphing bugs:**
- Event listeners and element state are lost between updates
- Event listeners and state are misplaced across wrong elements
- Entire Livewire components can be reset or duplicated
- Alpine components and state can be lost or misplaced

## Solutions

### Internal Look-Ahead

Livewire has an additional step in its morphing algorithm that checks subsequent elements and their contents before changing an element, preventing many morphing issues.

### Injecting Morph Markers

Livewire automatically detects conditionals inside Blade templates and wraps them in HTML comment markers:

```html
<form wire:submit="save">
    <div>
        <input wire:model="title">
    </div>

    <!--[if BLOCK]><![endif]-->
    @if ($errors->has('title'))
        <div>Error: {{ $errors->first('title') }}</div>
    @endif
    <!--[if ENDBLOCK]><![endif]-->

    <div>
        <button>Save</button>
    </div>
</form>
```

To disable this feature, update `config/livewire.php`:

```php
'inject_morph_markers' => false,
```

### Wrapping Conditionals

The most reliable way to avoid morphing problems is to wrap conditionals and loops in their own elements that are always present:

```html
<form wire:submit="save">
    <div>
        <input wire:model="title">
    </div>

    <div>
        @if ($errors->has('title'))
            <div>{{ $errors->first('title') }}</div>
        @endif
    </div>

    <div>
        <button>Save</button>
    </div>
</form>
```

## Bypassing Morphing

Use `wire:replace` to instruct Livewire to replace all children of an element instead of attempting to morph:

```html
<div wire:replace>
    <!-- This element and its children will be completely replaced -->
</div>
```

## See Also

- [Hydration](./hydration.md) — Understand Livewire's request lifecycle
- [wire:replace](../html-directives/wire-replace.md) — Bypass morphing for specific elements
