# wire:click

**Source URL:** https://livewire.laravel.com/docs/4.x/wire-click

## Overview

Livewire provides a simple `wire:click` directive for calling component methods (aka actions) when a user clicks a specific element on the page.

## Basic Usage

For example, given the ShowInvoice component below:

```php
<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Invoice;

class ShowInvoice extends Component
{
    public Invoice $invoice;

    public function download()
    {
        return response()->download(
            $this->invoice->file_path, 'invoice.pdf'
        );
    }
}
```

You can trigger the `download()` method from the class above when a user clicks a "Download Invoice" button by adding `wire:click="download"`:

```html
<button type="button" wire:click="download">
    Download Invoice
</button>
```

## Passing Parameters

You can pass parameters to actions directly in the `wire:click` directive:

```html
<button wire:click="delete({{ $post->id }})">Delete</button>
```

When the button is clicked, the `delete()` method will be called with the post's ID.

**Important:** Action parameters should be treated like HTTP request input and should not be trusted. Always authorize ownership before updating data.

## Using on Links

When using `wire:click` on `<a>` tags, you must append `.prevent` to prevent the default link behavior. Otherwise, the browser will navigate to the provided href.

```html
<a href="#" wire:click.prevent="show">View Details</a>
```

## Preventing Re-renders

Use `.renderless` to skip re-rendering the component after the action completes. This is useful for actions that only perform side effects (like logging or analytics):

```html
<button wire:click.renderless="trackClick">Track Event</button>
```

## Preserving Scroll Position

By default, updating content may change the scroll position. Use `.preserve-scroll` to maintain the current scroll position:

```html
<button wire:click.preserve-scroll="loadMore">Load More</button>
```

## Parallel Execution

By default, Livewire queues actions within the same component. Use `.async` to allow actions to run in parallel:

```html
<button wire:click.async="process">Process</button>
```

## Reference

**Syntax:**
- `wire:click="methodName"`
- `wire:click="methodName(param1, param2)"`

## Modifiers

| Modifier | Description |
|----------|-------------|
| `.prevent` | Prevents default browser behavior |
| `.stop` | Stops event propagation |
| `.self` | Only triggers if event originated on this element |
| `.once` | Ensures listener is only called once |
| `.debounce` | Debounces handler by 250ms (use `.debounce.500ms` for custom duration) |
| `.throttle` | Throttles handler to every 250ms minimum (use `.throttle.500ms` for custom) |
| `.window` | Listens for event on the window object |
| `.document` | Listens for event on the document object |
| `.outside` | Only listens for clicks outside the element |
| `.passive` | Won't block scroll performance |
| `.capture` | Listens during the capturing phase |
| `.camel` | Converts event name to camel case |
| `.dot` | Converts event name to dot notation |
| `.renderless` | Skips re-rendering after action completes |
| `.preserve-scroll` | Maintains scroll position during updates |
| `.async` | Executes action in parallel instead of queued |

## See Also

- [Actions](../essentials/actions.md) — Complete guide to component actions
- [Events](../essentials/events.md) — Dispatch events from click handlers
- [wire:confirm](./wire-confirm.md) — Add confirmation dialogs to actions
