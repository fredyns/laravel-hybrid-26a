# Isolate

**Source URL:** https://livewire.laravel.com/docs/4.x/attribute-isolate

## Overview

The `#[Isolate]` attribute prevents a component's requests from being bundled with other component updates, allowing it to execute in parallel.

## Why Bundling Matters

Every component update in Livewire triggers a network request. By default, when multiple components trigger updates at the same time, they are bundled into a single request.

This results in fewer network connections to the server and can drastically reduce server load. In addition to the performance gains, this also unlocks features internally that require collaboration between multiple components (Reactive Properties, Modelable Properties, etc.)

However, there are times when disabling this bundling is desired for performance reasons. That's where `#[Isolate]` comes in.

## Basic Usage

Apply the `#[Isolate]` attribute to any component that should send isolated requests:

```php
<?php // resources/views/components/post/⚡show.blade.php

use Livewire\Attributes\Isolate;
use Livewire\Component;
use App\Models\Post;

new #[Isolate] class extends Component {
    public Post $post;

    public function refreshStats()
    {
        // Expensive operation...
        $this->post->recalculateStatistics();
    }
};
```

With `#[Isolate]`, this component's requests will no longer be bundled with other component updates, allowing them to execute in parallel.

**Note:** Bundling is great for most scenarios, but if a component performs expensive operations, bundling can slow down the entire request. Isolating that component allows it to run in parallel with other updates.

## When to Use

Use `#[Isolate]` when:

- The component performs expensive operations (complex queries, API calls, heavy computations)
- Multiple components use `wire:poll` and you want independent polling intervals
- Components listen for events and you don't want one slow component to block others
- The component doesn't need to coordinate with other components on the page

## Example: Polling Components

Here's a practical example with multiple polling components:

```php
<?php // resources/views/components/⚡system-status.blade.php

use Livewire\Attributes\Isolate;
use Livewire\Component;

new #[Isolate] class extends Component {
    public function checkStatus()
    {
        // Expensive external API call...
        return ExternalService::getStatus();
    }
};
```

```html
<div wire:poll.5s>
    Status: {{ $this->checkStatus() }}
</div>
```

Without `#[Isolate]`, this component's slow API call would delay other components on the page. With it, the component polls independently without blocking others.

## Lazy Components are Isolated by Default

When using the `#[Lazy]` attribute, components are automatically isolated to load in parallel. You can disable this behavior if needed:

```php
<?php // resources/views/components/⚡revenue.blade.php

use Livewire\Attributes\Lazy;
use Livewire\Component;

new #[Lazy(isolate: false)] class extends Component {
    // ...
};
```

Now multiple revenue components will bundle their lazy-load requests into a single network request.

## Trade-offs

**Benefits:**
- Prevents slow components from blocking other updates
- Allows true parallel execution of expensive operations
- Independent polling and event handling

**Drawbacks:**
- More network requests to the server
- Can't coordinate with other components in the same request
- Slightly higher server overhead from multiple connections

## See Also

- [Lazy Loading](../features/lazy.md) — Defer component rendering
- [Components](../essentials/components.md) — Learn about Livewire components
