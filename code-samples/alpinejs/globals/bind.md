# Alpine.bind()

**Source URL:** https://alpinejs.dev/globals/alpine-bind

## Overview

`Alpine.bind()` provides a way to re-use `x-bind` objects within your application.

Here's a simple example. Rather than binding attributes manually with Alpine:

```html
<button type="button" @click="doSomething()" :disabled="shouldDisable"></button>
```

You can bundle these attributes up into a reusable object and use `x-bind` to bind to that:

```html
<button x-bind="SomeButton"></button>
 
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.bind('SomeButton', () => ({
            type: 'button',
 
            '@click'() {
                this.doSomething()
            },
 
            ':disabled'() {
                return this.shouldDisable
            },
        }))
    })
</script>
```

---

## See Also

- [x-bind](../directives/bind.md) — Bind attributes
- [Alpine.data()](./data.md) — Register reusable components
- [Alpine.store()](./store.md) — Global state management
