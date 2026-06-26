# Persist Plugin

**Source URL:** https://alpinejs.dev/plugins/persist

## Overview

Alpine's Persist plugin allows you to persist Alpine state across page loads.

This is useful for persisting search filters, active tabs, and other features where users will be frustrated if their configuration is reset after refreshing or leaving and revisiting a page.

---

## Installation

You can use this plugin by either including it from a `<script>` tag or installing it via NPM:

### Via CDN

You can include the CDN build of this plugin as a `<script>` tag, just make sure to include it BEFORE Alpine's core JS file.

```html
<!-- Alpine Plugins -->
<script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
 
<!-- Alpine Core -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
```

### Via NPM

You can install Persist from NPM for use inside your bundle like so:

```bash
npm install @alpinejs/persist
```

Then initialize it from your bundle:

```javascript
import Alpine from 'alpinejs'
import persist from '@alpinejs/persist'
 
Alpine.plugin(persist)
```

---

## $persist

The primary API for using this plugin is the magic `$persist` method.

You can wrap any value inside `x-data` with `$persist` like below to persist its value across page loads:

```html
<div x-data="{ count: $persist(0) }">
    <button x-on:click="count++">Increment</button>
 
    <span x-text="count"></span>
</div>
```

In the above example, because we wrapped `0` in `$persist()`, Alpine will now intercept changes made to `count` and persist them across page loads.

---

## How Does It Work?

If a value is wrapped in `$persist`, on initialization Alpine will register its own watcher for that value. Now everytime that value changes for any reason, Alpine will store the new value in localStorage.

Now when a page is reloaded, Alpine will check localStorage (using the name of the property as the key) for a value. If it finds one, it will set the property value from localStorage immediately.

Alpine prefixes the property name with "x" as a way of namespacing these values so Alpine doesn't conflict with other tools using localStorage.

> **Note:** `$persist` works with primitive values as well as with arrays and objects. However, it is worth noting that localStorage must be cleared when the type of the variable changes.

---

## Setting a Custom Key

By default, Alpine uses the property key that `$persist(...)` is being assigned to ("count" in the above examples).

Consider the scenario where you have multiple Alpine components across pages or even on the same page that all use "count" as the property key. Alpine will have no way of differentiating between these components.

In these cases, you can set your own custom key for any persisted value using the `.as` modifier like so:

```html
<div x-data="{ count: $persist(0).as('other-count') }">
    <button x-on:click="count++">Increment</button>
 
    <span x-text="count"></span>
</div>
```

Now Alpine will store and retrieve the above "count" value using the key "other-count".

---

## Using a Custom Storage

By default, data is saved to localStorage, it does not have an expiration time and it's kept even when the page is closed.

Consider the scenario where you want to clear the data once the user closes the tab. In this case you can persist data to sessionStorage using the `.using` modifier like so:

```html
<div x-data="{ count: $persist(0).using(sessionStorage) }">
    <button x-on:click="count++">Increment</button>
 
    <span x-text="count"></span>
</div>
```

You can also define your custom storage object exposing a `getItem` function and a `setItem` function. For example, you can decide to use a session cookie as storage doing so:

```html
<script>
    window.cookieStorage = {
        getItem(key) {
            let cookies = document.cookie.split(";");
            for (let i = 0; i < cookies.length; i++) {
                let cookie = cookies[i].split("=");
                if (key == cookie[0].trim()) {
                    return decodeURIComponent(cookie[1]);
                }
            }
            return null;
        },
        setItem(key, value) {
            document.cookie = key+' = '+encodeURIComponent(value)
        }
    }
</script>
 
<div x-data="{ count: $persist(0).using(cookieStorage) }">
    <button x-on:click="count++">Increment</button>
 
    <span x-text="count"></span>
</div>
```

---

## Using $persist with Alpine.data

If you want to use `$persist` with `Alpine.data`, you need to use a standard function instead of an arrow function so Alpine can bind a custom `this` context when it initially evaluates the component scope.

```javascript
Alpine.data('dropdown', function () {
    return {
        open: this.$persist(false)
    }
})
```

---

## Using the Alpine.$persist Global

`Alpine.$persist` is exposed globally so it can be used outside of `x-data` contexts. This is useful to persist data from other sources such as `Alpine.store`.

```javascript
Alpine.store('darkMode', {
    on: Alpine.$persist(true).as('darkMode_on')
});
```

---

## See Also

- [Plugins](../alpinejs-doc.md) — Alpine plugins overview
- [Alpine.store()](../globals/store.md) — Global state management
- [State](../essentials/state.md) — Managing component state
