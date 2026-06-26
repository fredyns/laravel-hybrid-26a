# Hydration

**Source URL:** https://livewire.laravel.com/docs/4.x/hydration

## Overview

Using Livewire feels like attaching a server-side PHP class directly to a web browser. However, in reality, Livewire behaves like a standard web application—it renders static HTML to the browser, listens for browser events, then makes AJAX requests to invoke server-side code.

Because each AJAX request is "stateless" (no long-running backend process keeps component state alive), Livewire must re-create the last-known state of a component before making updates.

Livewire does this by taking "snapshots" of the PHP component after each update. We refer to taking the snapshot as "dehydration" and re-creating a component from a snapshot as "hydration".

## Dehydrating

When Livewire dehydrates a server-side component, it does two things:

1. Renders the component's template to HTML
2. Creates a JSON snapshot of the component

### Rendering HTML

After a component is mounted or updated, Livewire calls the component's `render()` method to convert the Blade template to raw HTML.

Take the following counter component:

```php
<?php

use Livewire\Component;

new class extends Component {
    public $count = 1;

    public function increment()
    {
        $this->count++;
    }

    public function render()
    {
        return <<<'HTML'
        <div>
            Count: {{ $count }}

            <button wire:click="increment">+</button>
        </div>
        HTML;
    }
};
```

After each mount or update, Livewire renders this to:

```html
<div>
    Count: 1

    <button wire:click="increment">+</button>
</div>
```

### The Snapshot

To re-create the counter component on the server during the next request, a JSON snapshot is created:

```json
{
    "state": {
        "count": 1
    },
    "memo": {
        "name": "counter",
        "id": "1526456"
    }
}
```

The `memo` portion stores information needed to identify and re-create the component, while the `state` portion stores values of all public properties.

### Embedding the Snapshot in HTML

When a component is first rendered, Livewire stores the snapshot as JSON inside a `wire:snapshot` attribute:

```html
<div wire:id="..." wire:snapshot="{ state: {...}, memo: {...} }">
    Count: 1

    <button wire:click="increment">+</button>
</div>
```

## Hydrating

When a component update is triggered (e.g., the "+" button is pressed), a payload like the following is sent to the server:

```json
{
    "calls": [
        { "method": "increment", "params": [] }
    ],
    "snapshot": {
        "state": {
            "count": 1
        },
        "memo": {
            "name": "counter",
            "id": "1526456"
        }
    }
}
```

Before Livewire can call the `increment` method, it must first create a new counter instance and seed it with the snapshot's state:

```php
$state = request('snapshot.state');
$memo = request('snapshot.memo');

$instance = Livewire::new($memo['name'], $memo['id']);

foreach ($state as $property => $value) {
    $instance[$property] = $value;
}
```

## Advanced Hydration

The counter example demonstrates hydrating simple values like integers. However, Livewire supports more sophisticated property types.

Consider a todos component with a Laravel collection:

```php
<?php

use Livewire\Component;

new class extends Component {
    public $todos;

    public function mount() {
        $this->todos = collect([
            'first',
            'second',
            'third',
        ]);
    }
};
```

The snapshot's state object uses metadata tuples to represent complex types:

```json
{
    "state": {
        "todos": [
            ["first", "second", "third"],
            { "s": "clctn", "class": "Illuminate\\Support\\Collection" }
        ]
    }
}
```

When Livewire encounters a tuple during hydration, it uses the metadata in the second element to intelligently hydrate the state in the first:

```php
[$state, $metadata] = request('snapshot.state.todos');

$collection = new $metadata['class']($state);
```

### Deeply Nested Tuples

This approach supports deeply nested properties. For example, a collection containing a Stringable:

```php
<?php

use Livewire\Component;

new class extends Component {
    public $todos;

    public function mount() {
        $this->todos = collect([
            'first',
            'second',
            str('third'),
        ]);
    }
};
```

The dehydrated snapshot would be:

```json
{
    "todos": [
        [
            "first",
            "second",
            ["third", { "s": "str" }]
        ],
        { "s": "clctn", "class": "Illuminate\\Support\\Collection" }
    ]
}
```

## Supporting Custom Property Types

Livewire has hydration support for common PHP and Laravel types. For unsupported types, use Synthesizers—Livewire's mechanism for hydrating/dehydrating custom property types.

## See Also

- [Lifecycle Hooks](../essentials/lifecycle-hooks.md) — Use `hydrate()` and `dehydrate()` hooks
- [Properties](../essentials/properties.md) — How properties are preserved between requests
- [Morphing](./morphing.md) — How Livewire updates the DOM
- [Synthesizers](./synthesizers.md) — Customize property serialization
