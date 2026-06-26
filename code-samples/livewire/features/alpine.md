# Alpine

**Source URL:** https://livewire.laravel.com/docs/4.x/alpine

## Overview

AlpineJS is a lightweight JavaScript library that makes it easy to add client-side interactivity to your web pages. It was originally built to complement tools like Livewire where a more JavaScript-centric utility is helpful for sprinkling interactivity around your app.

Livewire ships with Alpine out of the box so there is no need to install it separately.

The best place to learn about using AlpineJS is the [Alpine documentation](https://alpinejs.dev).

## A Basic Alpine Component

To lay a foundation for the rest of this documentation, here is one of the most simple and informative examples of an Alpine component. A small "counter" that shows a number on the page and allows the user to increment that number by clicking a button:

```html
<!-- Declare a JavaScript object of data... -->
<div x-data="{ count: 0 }">
    <!-- Render the current "count" value inside an element... -->
    <h2 x-text="count"></h2>

    <!-- Increment the "count" value by "1" when a click event is dispatched... -->
    <button x-on:click="count++">+</button>
</div>
```

The Alpine component above can be used inside any Livewire component in your application without a hitch. Livewire takes care of maintaining Alpine's state across Livewire component updates. In essence, you should feel free to use Alpine components inside Livewire as if you were using Alpine in any other non-Livewire context.

## Using Alpine Inside Livewire

Let's explore a more real-life example of using an Alpine component inside a Livewire component.

Below is a simple Livewire component showing the details of a post model from the database. By default, only the title of the post is shown:

```html
<div>
    <h1>{{ $post->title }}</h1>

    <div x-data="{ expanded: false }">
        <button type="button" x-on:click="expanded = ! expanded">
            <span x-show="! expanded">Show post content...</span>
            <span x-show="expanded">Hide post content...</span>
        </button>

        <div x-show="expanded">
            {{ $post->content }}
        </div>
    </div>
</div>
```

By using Alpine, we can hide the content of the post until the user presses the "Show post content..." button. At that point, Alpine's `expanded` property will be set to true and the content will be shown on the page because `x-show="expanded"` is used to give Alpine control over the visibility of the post's content.

This is an example of where Alpine shines: adding interactivity into your application without triggering Livewire server-roundtrips.

## Controlling Livewire from Alpine using $wire

One of the most powerful features available to you as a Livewire developer is `$wire`. The `$wire` object is a magic object available to all your Alpine components that are used inside of Livewire.

You can think of `$wire` as a gateway from JavaScript into PHP. It allows you to access and modify Livewire component properties, call Livewire component methods, and do much more; all from inside AlpineJS.

### Accessing Livewire Properties

Here is an example of a simple "character count" utility in a form for creating a post. This will instantly show a user how many characters are contained inside their post's content as they type:

```html
<form wire:submit="save">
    <!-- ... -->

    <input wire:model="content" type="text">

    <small>
        Character count: <span x-text="$wire.content.length"></span>
    </small>

    <button type="submit">Save</button>
</form>
```

As you can see `x-text` in the above example is being used to allow Alpine to control the text content of the `<span>` element. `x-text` accepts any JavaScript expression inside of it and automatically reacts when any dependencies are updated. Because we are using `$wire.content` to access the value of `$content`, Alpine will automatically update the text content every time `$wire.content` is updated from Livewire; in this case by `wire:model="content"`.

### Mutating Livewire Properties

Here is an example of using `$wire` inside Alpine to clear the "title" field of a form for creating a post:

```html
<form wire:submit="save">
    <input wire:model="title" type="text">

    <button type="button" x-on:click="$wire.title = ''">Clear</button>

    <!-- ... -->

    <button type="submit">Save</button>
</form>
```

As a user is filling out the above Livewire form, they can press "Clear" and the title field will be cleared without sending a network request from Livewire. The interaction will be "instant".

Here's a brief explanation of what's going on to make that happen:

1. `x-on:click` tells Alpine to listen for a click on the button element
2. When clicked, Alpine runs the provided JS expression: `$wire.title = ''`
3. Because `$wire` is a magic object representing the Livewire component, all properties from your component can be accessed or mutated straight from JavaScript
4. `$wire.title = ''` sets the value of `$title` in your Livewire component to an empty string
5. Any Livewire utilities like `wire:model` will instantly react to this change, all without sending a server-roundtrip
6. On the next Livewire network request, the `$title` property will be updated to an empty string on the backend

### Calling Livewire Methods

Alpine can also easily call any Livewire methods/actions by simply calling them directly on `$wire`.

Here is an example of using Alpine to listen for a "blur" event on an input and triggering a form save. The "blur" event is dispatched by the browser when a user presses "tab" to remove focus from the current element and focus on the next one on the page:

```html
<form wire:submit="save">
    <input wire:model="title" type="text" x-on:blur="$wire.save()">

    <!-- ... -->

    <button type="submit">Save</button>
</form>
```

Typically, you would just use `wire:model.live.blur="title"` in this situation, however, it's helpful for demonstration purposes how you can achieve this using Alpine.

### Passing Parameters

You can also pass parameters to Livewire methods by simply passing them to the `$wire` method call.

Consider a component with a `deletePost()` method like so:

```php
public function deletePost($postId)
{
    $post = Post::find($postId);

    // Authorize user can delete...
    auth()->user()->can('update', $post);

    $post->delete();
}
```

Now, you can pass a `$postId` parameter to the `deletePost()` method from Alpine like so:

```html
<button type="button" x-on:click="$wire.deletePost(1)">
    Delete
</button>
```

In general, something like a `$postId` would be generated in Blade. Here's an example of using Blade to determine which `$postId` Alpine passes into `deletePost()`:

```html
@foreach ($posts as $post)
    <button type="button" wire:key="{{ $post->id }}" x-on:click="$wire.deletePost({{ $post->id }})">
        Delete "{{ $post->title }}"
    </button>
@endforeach
```

If there are three posts on the page, the above Blade template will render to something like the following in the browser:

```html
<button type="button" x-on:click="$wire.deletePost(1)">
    Delete "The power of walking"
</button>

<button type="button" x-on:click="$wire.deletePost(2)">
    Delete "How to record a song"
</button>

<button type="button" x-on:click="$wire.deletePost(3)">
    Delete "Teach what you learn"
</button>
```

As you can see, we've used Blade to render different post IDs into the Alpine `x-on:click` expressions.

### Blade Parameter "Gotchas"

This is an extremely powerful technique, but can be confusing when reading your Blade templates. It can be hard to know which parts are Blade and which parts are Alpine at first glance. Therefore, it's helpful to inspect the HTML rendered on the page to make sure what you are expecting to be rendered is accurate.

Here's an example that commonly confuses people:

Let's say, instead of an ID, your Post model uses UUIDs for indexes (IDs are integers, and UUIDs are long strings of characters).

If we render the following just like we did with an ID there will be an issue:

```html
<!-- Warning: this is an example of problematic code... -->
<button
    type="button"
    x-on:click="$wire.deletePost({{ $post->uuid }})"
>
```

The above Blade template will render the following in your HTML:

```html
<!-- Warning: this is an example of problematic code... -->
<button
    type="button"
    x-on:click="$wire.deletePost(93c7b04c-c9a4-4524-aa7d-39196011b81a)"
>
```

Notice the lack of quotes around the UUID string? When Alpine goes to evaluate this expression, JavaScript will throw an error: "Uncaught SyntaxError: Invalid or unexpected token".

To fix this, we need to add quotations around the Blade expression like so:

```html
<button
    type="button"
    x-on:click="$wire.deletePost('{{ $post->uuid }}')"
>
```

Now the above template will render properly and everything will work as expected:

```html
<button
    type="button"
    x-on:click="$wire.deletePost('93c7b04c-c9a4-4524-aa7d-39196011b81a')"
>
```

## Refreshing a Component

You can easily refresh a Livewire component (trigger network roundtrip to re-render a component's Blade view) using `$wire.$refresh()`:

```html
<button type="button" x-on:click="$wire.$refresh()">
    Refresh
</button>
```

## Sharing State using $wire.entangle

**You probably don't need this**

In almost all cases, you should use `$wire` to directly access Livewire properties from Alpine instead of using `$wire.entangle()`. Entangling creates duplicate state that can cause predictability and performance issues. This API is maintained for backwards compatibility but is discouraged for new code.

**Do not use the `@entangle` Blade directive** - it has been deprecated and causes issues when removing DOM elements.

For rare cases where you need bidirectional state synchronization between Alpine and Livewire, you can use `$wire.entangle()`:

```html
<div x-data="{ open: $wire.entangle('showDropdown') }">
    <button x-on:click="open = true">Show More...</button>

    <ul x-show="open">
        <li><button wire:click="archive">Archive</button></li>
    </ul>
</div>
```

By default, changes are deferred until the next Livewire request. Use `.live` to sync immediately:

```html
<div x-data="{ open: $wire.entangle('showDropdown').live }">
    <!-- ... -->
</div>
```

## Using the @js Directive

If you need to output PHP data for use in Alpine directly, you can use the `@js` directive:

```html
<div x-data="{ posts: @js($posts) }">
    <!-- ... -->
</div>
```

## Manually Bundling Alpine in Your JavaScript Build

By default, Livewire and Alpine's JavaScript is injected onto each Livewire page automatically.

This is ideal for simpler setups, however, you may want to include your own Alpine components, stores, and plugins into your project.

To include Livewire and Alpine via your own JavaScript bundle on a page is straightforward.

First, you must include the `@livewireScriptConfig` directive in your layout file like so:

```html
<html>
<head>
    <!-- ... -->
    @livewireStyles
    @vite(['resources/js/app.js'])
</head>
<body>
    {{ $slot }}

    @livewireScriptConfig
</body>
</html>
```

This allows Livewire to provide your bundle with certain configuration it needs for your app to run properly.

Now you can import Livewire and Alpine in your `resources/js/app.js` file like so:

```javascript
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

// Register any Alpine directives, components, or plugins here...

Livewire.start()
```

Here is an example of registering a custom Alpine directive called "x-clipboard" in your application:

```javascript
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

Alpine.directive('clipboard', (el) => {
    let text = el.textContent

    el.addEventListener('click', () => {
        navigator.clipboard.writeText(text)
    })
})

Livewire.start()
```

Now the `x-clipboard` directive will be available to all your Alpine components in your Livewire application.

## See Also

- [Properties](../essentials/properties.md) — Access Livewire properties from Alpine using `$wire`
- [Actions](../essentials/actions.md) — Call Livewire actions from Alpine
- [JavaScript](../advanced/javascript.md) — Execute custom JavaScript in components
- [Events](../essentials/events.md) — Dispatch and listen for events with Alpine
