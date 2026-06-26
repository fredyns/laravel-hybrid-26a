# Alpine.data()

**Source URL:** https://alpinejs.dev/globals/alpine-data

## Overview

`Alpine.data()` provides a way to re-use `x-data` contexts within your application.

Here's a contrived dropdown component for example:

```html
<div x-data="dropdown">
    <button @click="toggle">...</button>
 
    <div x-show="open">...</div>
</div>
 
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('dropdown', () => ({
            open: false,
 
            toggle() {
                this.open = ! this.open
            }
        }))
    })
</script>
```

As you can see we've extracted the properties and methods we would usually define directly inside `x-data` into a separate Alpine component object.

---

## Registering from a Bundle

If you've chosen to use a build step for your Alpine code, you should register your components in the following way:

```javascript
import Alpine from 'alpinejs'
import dropdown from './dropdown.js'
 
Alpine.data('dropdown', dropdown)
 
Alpine.start()
```

This assumes you have a file called `dropdown.js` with the following contents:

```javascript
export default () => ({
    open: false,
 
    toggle() {
        this.open = ! this.open
    }
})
```

---

## Initial Parameters

In addition to referencing `Alpine.data` providers by their name plainly (like `x-data="dropdown"`), you can also reference them as functions (`x-data="dropdown()"`). By calling them as functions directly, you can pass in additional parameters to be used when creating the initial data object like so:

```html
<div x-data="dropdown(true)">
```

```javascript
Alpine.data('dropdown', (initialOpenState = false) => ({
    open: initialOpenState
}))
```

Now, you can re-use the dropdown object, but provide it with different parameters as you need to.

---

## Init Functions

If your component contains an `init()` method, Alpine will automatically execute it before it renders the component. For example:

```javascript
Alpine.data('dropdown', () => ({
    init() {
        // This code will be executed before Alpine
        // initializes the rest of the component.
    }
}))
```

---

## Destroy Functions

If your component contains a `destroy()` method, Alpine will automatically execute it before cleaning up the component.

A primary example for this is when registering an event handler with another library or a browser API that isn't available through Alpine. See the following example code on how to use the `destroy()` method to clean up such a handler.

```javascript
Alpine.data('timer', () => ({
    timer: null,
    counter: 0,
    init() {
      // Register an event handler that references the component instance
      this.timer = setInterval(() => {
        console.log('Increased counter to', ++this.counter);
      }, 1000);
    },
    destroy() {
        // Detach the handler, avoiding memory and side-effect leakage
        clearInterval(this.timer);
    },
}))
```

An example where a component is destroyed is when using one inside an `x-if`:

```html
<span x-data="{ enabled: false }">
    <button @click.prevent="enabled = !enabled">Toggle</button>
 
    <template x-if="enabled">
        <span x-data="timer" x-text="counter"></span>
    </template>
</span>
```

---

## Using Magic Properties

If you want to access magic methods or properties from a component object, you can do so using the `this` context:

```javascript
Alpine.data('dropdown', () => ({
    open: false,
 
    init() {
        this.$watch('open', () => {...})
    }
}))
```

---

## Encapsulating Directives with x-bind

If you wish to re-use more than just the data object of a component, you can encapsulate entire Alpine template directives using `x-bind`.

The following is an example of extracting the templating details of our previous dropdown component using `x-bind`:

```html
<div x-data="dropdown">
    <button x-bind="trigger"></button>
 
    <div x-bind="dialogue"></div>
</div>
```

```javascript
Alpine.data('dropdown', () => ({
    open: false,
 
    trigger: {
        ['@click']() {
            this.open = ! this.open
        },
    },
 
    dialogue: {
        ['x-show']() {
            return this.open
        },
    },
}))
```

---

## See Also

- [x-data](../directives/data.md) — Declare reactive data
- [State](../essentials/state.md) — Managing component state
- [Alpine.store()](./store.md) — Global state management
