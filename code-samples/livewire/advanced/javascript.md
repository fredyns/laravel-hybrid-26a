# JavaScript

**Source URL:** https://livewire.laravel.com/docs/4.x/javascript

## Overview

Livewire and Alpine provide utilities for building dynamic components directly in HTML, however, there are times when it's helpful to break out and execute plain JavaScript for your component.

**Note:** Class-based components need the `@script` directive. Wrap your script tags with `@script` if you're using class-based components where the Blade view is in a separate file from the PHP class.

## Executing Scripts

You can add `<script>` tags directly inside your component template to execute JavaScript when the component loads.

Because these scripts are handled by Livewire, they execute at the perfect time—after the page has loaded, but before the Livewire component renders. This means you no longer need to wrap scripts in `document.addEventListener('...')`.

```html
<div>
    ...
</div>

<script>
    // This JavaScript will execute every time this component loads onto the page...
</script>
```

Here's an example registering a JavaScript action:

```html
<div>
    <button wire:click="$js.increment">+</button>
</div>

<script>
    this.$js.increment = () => {
        console.log('increment')
    }
</script>
```

## Using $wire from Scripts

When you add `<script>` tags inside your component, you automatically have access to your Livewire component's `$wire` object.

Example using `setInterval` to refresh the component every 2 seconds:

```html
<script>
    setInterval(() => {
        $wire.$refresh()
    }, 2000)
</script>
```

## The $wire Object

The `$wire` object is your JavaScript interface to your Livewire component. Essential methods include:

```javascript
// Access and modify properties
$wire.count
$wire.count = 5
$wire.$set('count', 5)

// Call component methods
$wire.save()
$wire.delete(postId)

// Refresh the component
$wire.$refresh()

// Dispatch events
$wire.$dispatch('post-created', { postId: 2 })

// Listen for events
$wire.$on('post-created', (event) => {
    console.log(event.postId)
})

// Access the root element
$wire.$el.querySelector('.modal')
```

## Loading Assets

Use `@assets` to load entire script and style assets with the component:

```html
<div>
    <input type="text" data-picker>
</div>

@assets
<script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js" defer></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/pikaday/css/pikaday.css">
@endassets

<script>
    new Pikaday({ field: $wire.$el.querySelector('[data-picker]') });
</script>
```

Livewire ensures `@assets` are loaded before scripts evaluate and only loaded once per page regardless of component instances.

## Interceptors

Intercept Livewire requests at three levels: action, message, and request.

### Action Interceptors

Fire for each method call:

```javascript
$wire.intercept(({ action, onSend, onCancel, onSuccess, onError, onFailure, onFinish }) => {
    // action.name        - Method name ('save', '$refresh', etc.)
    // action.params      - Method parameters
    // action.component   - Component instance
    // action.cancel()    - Cancel this action

    onSend(({ call }) => {
        // call: { method, params, metadata }
    })

    onSuccess((result) => {
        // result: Return value from PHP method
    })

    onError(({ response, body, preventDefault }) => {
        preventDefault() // Prevent error modal
    })

    onFinish(() => {
        // Runs after DOM morph completes
    })
})
```

### Message Interceptors

Fire for each component update:

```javascript
$wire.interceptMessage(({ message, cancel, onSend, onSuccess, onError, onFinish }) => {
    onSuccess(({ payload, onSync, onEffect, onMorph, onRender }) => {
        onSync(() => {})    // After state synced
        onEffect(() => {})  // After effects processed
        onMorph(async () => {})   // After DOM morphed
        onRender(() => {})  // After render complete
    })
})
```

### Request Interceptors

Fire for each HTTP request:

```javascript
$wire.interceptRequest(({ request, onSend, onSuccess, onError, onFinish }) => {
    onSuccess(({ response, body, json }) => {})
})
```

## See Also

- [Alpine](../features/alpine.md) — Alpine.js integration
- [Actions](../essentials/actions.md) — Handle component methods
