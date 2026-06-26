# Actions

**Source URL:** https://livewire.laravel.com/docs/4.x/actions

## Overview

Livewire actions are methods on your component that can be triggered by frontend interactions like clicking a button or submitting a form. They provide the developer experience of being able to call a PHP method directly from the browser, allowing you to focus on the logic of your application without getting bogged down writing boilerplate code connecting your application's frontend and backend.

### Basic Example

```php
<?php // resources/views/components/post/⚡create.blade.php

use Livewire\Component;
use App\Models\Post;

new class extends Component {
    public $title = '';

    public $content = '';

    public function save()
    {
        Post::create([
            'title' => $this->title,
            'content' => $this->content,
        ]);

        return redirect()->to('/posts');
    }
};
?>

<form wire:submit="save"> 
    <input type="text" wire:model="title">

    <textarea wire:model="content"></textarea>

    <button type="submit">Save</button>
</form>
```

In the above example, when a user submits the form by clicking "Save", `wire:submit` intercepts the submit event and calls the `save()` action on the server.

In essence, actions are a way to easily map user interactions to server-side functionality without the hassle of submitting and handling AJAX requests manually.

## Passing Parameters

Livewire allows you to pass parameters from your Blade template to the actions in your component, giving you the opportunity to provide an action additional data or state from the frontend when the action is called.

### Example: Passing Parameters to Actions

```php
<?php // resources/views/components/post/⚡index.blade.php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\Post;

new class extends Component {
    #[Computed]
    public function posts()
    {
        return Auth::user()->posts;
    }

    public function delete($id)
    {
        $post = Post::findOrFail($id);

        $this->authorize('delete', $post);

        $post->delete();
    }
};
```

```html
<div>
    @foreach ($this->posts as $post)
        <div wire:key="{{ $post->id }}">
            <h1>{{ $post->title }}</h1>
            <span>{{ $post->content }}</span>

            <button wire:click="delete({{ $post->id }})">Delete</button> 
        </div>
    @endforeach
</div>
```

For a post with an ID of 2, the "Delete" button in the Blade template above will render in the browser as:

```html
<button wire:click="delete(2)">Delete</button>
```

When this button is clicked, the `delete()` method will be called and `$id` will be passed in with a value of "2".

**Important:** Action parameters should be treated just like HTTP request input, meaning action parameter values should not be trusted. You should always authorize ownership of an entity before updating it in the database.

## Model Resolution

As an added convenience, you may automatically resolve Eloquent models by a corresponding model ID that is provided to an action as a parameter. This is very similar to route model binding. To get started, type-hint an action parameter with a model class and the appropriate model will automatically be retrieved from the database and passed to the action instead of the ID:

```php
<?php // resources/views/components/post/⚡index.blade.php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\Post;

new class extends Component {
    #[Computed]
    public function posts()
    {
        return Auth::user()->posts;
    }

    public function delete(Post $post) 
    {
        $this->authorize('delete', $post);

        $post->delete();
    }
};
```

## Dependency Injection

You can take advantage of Laravel's dependency injection system by type-hinting parameters in your action's signature. Livewire and Laravel will automatically resolve the action's dependencies from the container:

```php
<?php // resources/views/components/post/⚡index.blade.php

use Illuminate\Support\Facades\Auth;
use App\Repositories\PostRepository;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    #[Computed]
    public function posts()
    {
        return Auth::user()->posts;
    }

    public function delete(PostRepository $posts, $postId) 
    {
        $posts->deletePost($postId);
    }
};
```

```html
<div>
    @foreach ($this->posts as $post)
        <div wire:key="{{ $post->id }}">
            <h1>{{ $post->title }}</h1>
            <span>{{ $post->content }}</span>

            <button wire:click="delete({{ $post->id }})">Delete</button> 
        </div>
    @endforeach
</div>
```

In this example, the `delete()` method receives an instance of `PostRepository` resolved via Laravel's service container before receiving the provided `$postId` parameter.

## Event Listeners

Livewire supports a variety of event listeners, allowing you to respond to various types of user interactions:

| Listener | Description |
| --- | --- |
| `wire:click` | Triggered when an element is clicked |
| `wire:submit` | Triggered when a form is submitted |
| `wire:keydown` | Triggered when a key is pressed down |
| `wire:keyup` | Triggered when a key is released |
| `wire:mouseenter` | Triggered when the mouse enters an element |
| `wire:*` | Whatever text follows `wire:` will be used as the event name of the listener |

Because the event name after `wire:` can be anything, Livewire supports any browser event you might need to listen for. For example, to listen for `transitionend`, you can use `wire:transitionend`.

## Listening for Specific Keys

You can use one of Livewire's convenient aliases to narrow down key press event listeners to a specific key or combination of keys.

For example, to perform a search when a user hits Enter after typing into a search box, you can use `wire:keydown.enter`:

```html
<input wire:model="query" wire:keydown.enter="searchPosts">
```

You can chain more key aliases after the first to listen for combinations of keys. For example, if you would like to listen for the Enter key only while the Shift key is pressed, you may write the following:

```html
<input wire:keydown.shift.enter="...">
```

### Available Key Modifiers

| Modifier | Key |
| --- | --- |
| `.shift` | Shift |
| `.enter` | Enter |
| `.space` | Space |
| `.ctrl` | Ctrl |
| `.cmd` | Cmd |
| `.meta` | Cmd on Mac, Windows key on Windows |
| `.alt` | Alt |
| `.up` | Up arrow |
| `.down` | Down arrow |
| `.left` | Left arrow |
| `.right` | Right arrow |
| `.escape` | Escape |
| `.tab` | Tab |
| `.caps-lock` | Caps Lock |
| `.equal` | Equal, = |
| `.period` | Period, . |
| `.slash` | Forward Slash, / |

## Event Handler Modifiers

Livewire also includes helpful modifiers to make common event-handling tasks trivial.

For example, if you need to call `event.preventDefault()` from inside an event listener, you can suffix the event name with `.prevent`:

```html
<input wire:keydown.prevent="...">
```

### Available Event Listener Modifiers

| Modifier | Function |
| --- | --- |
| `.prevent` | Equivalent of calling `.preventDefault()` |
| `.stop` | Equivalent of calling `.stopPropagation()` |
| `.window` | Listens for event on the window object |
| `.outside` | Only listens for clicks "outside" the element |
| `.document` | Listens for events on the document object |
| `.once` | Ensures the listener is only called once |
| `.debounce` | Debounce the handler by 250ms as a default |
| `.debounce.100ms` | Debounce the handler for a specific amount of time |
| `.throttle` | Throttle the handler to being called every 250ms at minimum |
| `.throttle.100ms` | Throttle the handler at a custom duration |
| `.self` | Only call listener if event originated on this element, not children |
| `.camel` | Converts event name to camel case (`wire:custom-event` -> "customEvent") |
| `.dot` | Converts event name to dot notation (`wire:custom-event` -> "custom.event") |
| `.passive` | `wire:touchstart.passive` won't block scroll performance |
| `.capture` | Listen for event in the "capturing" phase |

Because `wire:` uses Alpine's `x-on` directive under the hood, these modifiers are made available to you by Alpine.

## Handling Third-Party Events

Livewire also supports listening for custom events fired by third-party libraries.

For example, let's imagine you're using the Trix rich text editor in your project and you want to listen for the `trix-change` event to capture the editor's content. You can accomplish this using the `wire:trix-change` directive:

```html
<form wire:submit="save">
    <!-- ... -->

    <trix-editor
        wire:trix-change="setPostContent"
    ></trix-editor>

    <button type="submit">Save</button>
</form>
```

```php
<?php

use Livewire\Component;

new class extends Component {
    public $content = '';

    public function setPostContent($content)
    {
        $this->content = $content;
    }

    // ...
};
```

## Async Actions

By default, Livewire actions are synchronous—the browser waits for the server to respond before the action completes. However, you can mark an action as asynchronous using the `#[Async]` attribute, allowing the browser to continue without waiting for the server response:

```php
use Livewire\Attributes\Async;
use Livewire\Component;

new class extends Component {
    #[Async]
    public function sendEmail()
    {
        Mail::send(...);
    }

    // ...
};
```

```html
<button wire:click="sendEmail">Send Email</button>
```

When the user clicks "Send Email", the action will be triggered but the browser won't wait for a response. This is useful for fire-and-forget operations like sending emails or logging analytics.

### When to Use Async Actions

Async actions are best used for operations that don't affect your component's rendered state. For example:

- Sending emails
- Logging events
- Triggering webhooks
- Updating external services

### When NOT to Use Async Actions

**Important:** Do not use async actions for operations that mutate component state. If a user rapidly triggers an async action that modifies properties, you may experience race conditions and lost updates.

```php
// Warning: Don't do this!
use Livewire\Attributes\Async;
use Livewire\Component;

new class extends Component {
    public $count = 0;

    #[Async] // Don't do this!
    public function increment()
    {
        $this->count++; // State mutation in an async action
    }

    // ...
};
```

If a user rapidly clicks the increment button, multiple async requests will fire simultaneously. Each request starts with the same initial `$count` value, leading to lost updates. You might click 5 times but only see the counter increment by 1.

The rule of thumb: Only use async for actions that perform pure side effects—operations that don't change any properties that affect your component's view.

### Fetching Data for JavaScript

Another valid use case is fetching data from the server that will be consumed entirely by JavaScript, without affecting your component's rendered state:

```php
<?php

use Livewire\Attributes\Async;
use Livewire\Component;

new class extends Component {
    #[Async]
    public function fetchSuggestions($query)
    {
        return Post::where('title', 'like', "%{$query}%")
            ->limit(5)
            ->pluck('title');
    }

    // ...
};
```

```html
<div x-data="{ suggestions: [] }">
    <input
        type="text"
        x-on:input.debounce="suggestions = await $wire.fetchSuggestions($event.target.value)"
    >

    <template x-for="suggestion in suggestions">
        <div x-text="suggestion"></div>
    </template>
</div>
```

Because the suggestions are stored purely in Alpine's `suggestions` data and never in Livewire's component state, it's safe to fetch them asynchronously.

## Preserving Scroll Position

When updating content, the browser may jump to a different scroll position. The `.preserve-scroll` modifier maintains the current scroll position during updates:

```html
<button wire:click.preserve-scroll="loadMore">Load More</button>

<select wire:model.live.preserve-scroll="category">...</select>
```

This is useful for infinite scroll, filters, and dynamic content updates where you don't want the page to jump.

## Security Concerns

Remember that any public method in your Livewire component can be called from the client-side, even without an associated `wire:click` handler that invokes it. In these scenarios, users can still trigger the action from the browser's DevTools.

### Always Authorize Action Parameters

Just like controller request input, it's imperative to authorize action parameters since they are arbitrary user input.

**Vulnerable Example:**

```php
<?php // resources/views/components/post/⚡index.blade.php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\Post;

new class extends Component {
    #[Computed]
    public function posts()
    {
        return Auth::user()->posts;
    }

    public function delete($id)
    {
        $post = Post::find($id);

        $post->delete();
    }
};
```

A malicious user can call `delete()` directly from a JavaScript console, passing any parameters they would like to the action. This means that a user viewing one of their posts can delete another user's post by passing the un-owned post ID to `delete()`.

**Secure Version:**

```php
<?php // resources/views/components/post/⚡index.blade.php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\Post;

new class extends Component {
    #[Computed]
    public function posts()
    {
        return Auth::user()->posts;
    }

    public function delete($id)
    {
        $post = Post::find($id);

        $this->authorize('delete', $post); 

        $post->delete();
    }
};
```

### Always Authorize Server-Side

Like standard Laravel controllers, Livewire actions can be called by any user, even if there isn't an affordance for invoking the action in the UI.

**Vulnerable Example:**

```php
<?php // resources/views/components/post/⚡index.blade.php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\Post;

new class extends Component {
    #[Computed]
    public function posts()
    {
        return Auth::user()->posts;
    }

    public function deletePost($id)
    {
        $post = Post::find($id);

        $post->delete();
    }
};
```

```html
<div>
    @foreach ($this->posts as $post)
        <div wire:key="{{ $post->id }}">
            <h1>{{ $post->title }}</h1>
            <span>{{ $post->content }}</span>

            @if (Auth::user()->isAdmin())
                <button wire:click="deletePost({{ $post->id }})">Delete</button>
            @endif
        </div>
    @endforeach
</div>
```

Any user can call `deletePost()` on the component from the browser's DevTools.

**Secure Version:**

```php
<?php // resources/views/components/post/⚡index.blade.php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\Post;

new class extends Component {
    #[Computed]
    public function posts()
    {
        return Auth::user()->posts;
    }

    public function deletePost($id)
    {
        if (! Auth::user()->isAdmin) { 
            abort(403);
        }

        $post = Post::find($id);

        $post->delete();
    }
};
```

### Keep Dangerous Methods Protected or Private

Every public method inside your Livewire component is callable from the client. Even methods you haven't referenced inside a `wire:click` handler. To prevent a user from calling a method that isn't intended to be callable client-side, you should mark them as protected or private.

**Vulnerable Example:**

```php
<?php // resources/views/components/post/⚡index.blade.php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\Post;

new class extends Component {
    #[Computed]
    public function posts()
    {
        return Auth::user()->posts;
    }

    public function deletePost($id)
    {
        if (! Auth::user()->isAdmin) {
            abort(403);
        }

        $this->delete($id); 
    }

    public function delete($postId)  
    {
        $post = Post::find($postId);

        $post->delete();
    }
};
```

Even though the `delete()` method isn't referenced anywhere in the template, if a user gained knowledge of its existence, they would be able to call it from the browser's DevTools because it is public.

**Secure Version:**

```php
<?php // resources/views/components/post/⚡index.blade.php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\Post;

new class extends Component {
    #[Computed]
    public function posts()
    {
        return Auth::user()->posts;
    }

    public function deletePost($id)
    {
        if (! Auth::user()->isAdmin) {
            abort(403);
        }

        $this->delete($id);
    }

    protected function delete($postId) 
    {
        $post = Post::find($postId);

        $post->delete();
    }
};
```

Once the method is marked as protected or private, an error will be thrown if a user tries to invoke it.

## See Also

- [Events](./events.md) — Communicate between components using events
- [Forms](./forms.md) — Handle form submissions with actions
- [Loading States](../features/loading-states.md) — Show feedback while actions are processing
- [wire:click](../html-directives/wire-click.md) — Trigger actions from button clicks
- [Validation](../features/validation.md) — Validate data before processing actions
