# wire:submit

**Source URL:** https://livewire.laravel.com/docs/4.x/wire-submit

## Overview

Livewire makes it easy to handle form submissions via the `wire:submit` directive. By adding `wire:submit` to a `<form>` element, Livewire will intercept the form submission, prevent the default browser handling, and call any Livewire component method.

## Basic Usage

Here's a basic example of using `wire:submit` to handle a "Create Post" form submission:

```php
<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Post;

class CreatePost extends Component
{
    public $title = '';

    public $content = '';

    public function save()
    {
        Post::create([
            'title' => $this->title,
            'content' => $this->content,
        ]);

        $this->redirect('/posts');
    }

    public function render()
    {
        return view('livewire.create-post');
    }
}
```

```html
<form wire:submit="save">
    <input type="text" wire:model="title">

    <textarea wire:model="content"></textarea>

    <button type="submit">Save</button>
</form>
```

In the above example, when a user submits the form by clicking "Save", `wire:submit` intercepts the submit event and calls the `save()` action on the server.

## Important Behaviors

**Livewire automatically calls preventDefault():** `wire:submit` is different than other Livewire event handlers in that it internally calls `event.preventDefault()` without the need for the `.prevent` modifier. This is because there are very few instances you would be listening for the submit event and NOT want to prevent its default browser handling (performing a full form submission to an endpoint).

**Livewire automatically disables forms while submitting:** By default, when Livewire is sending a form submission to the server, it will disable form submit buttons and mark all form inputs as readonly. This way a user cannot submit the same form again until the initial submission is complete.

## Reference

**Syntax:**
- `wire:submit="methodName"`
- `wire:submit="methodName(param1, param2)"`

## Modifiers

| Modifier | Description |
|----------|-------------|
| `.prevent` | Prevents default browser behavior (automatic for wire:submit) |
| `.stop` | Stops event propagation |
| `.self` | Only triggers if event originated on this element |
| `.once` | Ensures listener is only called once |
| `.debounce` | Debounces handler by 250ms (use `.debounce.500ms` for custom duration) |
| `.throttle` | Throttles handler to every 250ms minimum (use `.throttle.500ms` for custom) |
| `.window` | Listens for event on the window object |
| `.document` | Listens for event on the document object |
| `.passive` | Won't block scroll performance |
| `.capture` | Listens during the capturing phase |
| `.renderless` | Skips re-rendering after action completes |
| `.preserve-scroll` | Maintains scroll position during updates |
| `.async` | Executes action in parallel instead of queued |

## See Also

- [Forms](../essentials/forms.md) — Handle form submissions with Livewire
- [Actions](../essentials/actions.md) — Process form data in actions
- [Validation](../features/validation.md) — Validate forms before submission
