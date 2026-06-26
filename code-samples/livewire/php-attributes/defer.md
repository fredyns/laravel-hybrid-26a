# Defer

**Source URL:** https://livewire.laravel.com/docs/4.x/attribute-defer

## Overview

The `#[Defer]` attribute makes a component load immediately after the initial page load is complete, preventing slow components from blocking the page render.

## Basic Usage

Apply the `#[Defer]` attribute to any component that should be deferred:

```php
<?php // resources/views/components/⚡revenue.blade.php

use Livewire\Attributes\Defer;
use Livewire\Component;
use App\Models\Transaction;

new #[Defer] class extends Component {
    public $amount;

    public function mount()
    {
        // Slow database query...
        $this->amount = Transaction::monthToDate()->sum('amount');
    }
};
?>

<div>
    Revenue this month: {{ $amount }}
</div>
```

With `#[Defer]`, the component initially renders as an empty `<div></div>`, then loads immediately after the page finishes loading—without waiting for it to enter the viewport.

## Lazy vs Defer

Livewire provides two ways to delay component loading:

- **Lazy loading (#[Lazy])** - Components load when they become visible in the viewport (when the user scrolls to them)
- **Deferred loading (#[Defer])** - Components load immediately after the initial page load is complete

Both prevent slow components from blocking your initial page render, but differ in when the component actually loads.

## Rendering Placeholders

By default, Livewire renders an empty `<div></div>` before the component loads. You can provide a custom placeholder using the `placeholder()` method:

```php
<?php // resources/views/components/⚡revenue.blade.php

use Livewire\Attributes\Defer;
use Livewire\Component;
use App\Models\Transaction;

new #[Defer] class extends Component {
    public $amount;

    public function mount()
    {
        $this->amount = Transaction::monthToDate()->sum('amount');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <div>
            <svg><!-- Loading spinner... --></svg>
        </div>
        HTML;
    }
};
?>

<div>
    Revenue this month: {{ $amount }}
</div>
```

Users will see the loading spinner until the component fully loads.

**Note:** Match placeholder element type - If your placeholder's root element is a `<div>`, your component must also use a `<div>` element.

## Bundling Requests

By default, deferred components load in parallel with independent network requests. To bundle multiple deferred components into a single request, use the `bundle` parameter:

```php
<?php // resources/views/components/⚡revenue.blade.php

use Livewire\Attributes\Defer;
use Livewire\Component;

new #[Defer(bundle: true)] class extends Component {
    // ...
};
```

Now, if there are ten revenue components on the page, all ten will load via a single bundled network request instead of ten parallel requests.

## Alternative Approach

### Using the defer Parameter

Instead of the attribute, you can defer specific component instances using the `defer` parameter:

```html
<livewire:revenue defer />
```

This is useful when you only want certain instances of a component to be deferred.

### Overriding the Attribute

If a component has `#[Defer]` but you want to load it immediately in certain cases, you can override it:

```html
<livewire:revenue :defer="false" />
```

## When to Use

Use `#[Defer]` when:

- Components contain slow operations (database queries, API calls) that would delay page load
- The component is always visible on initial page load (if it's below the fold, use `#[Lazy]` instead)
- You want to improve perceived performance by showing the page faster

## Reference

```php
#[Defer(
    bool|null $bundle = null,
)]
```

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `$bundle` | bool\|null | null | Bundle multiple deferred components into a single network request |

## See Also

- [Lazy Loading](../features/lazy.md) — Defer component rendering
- [Components](../essentials/components.md) — Learn about Livewire components
