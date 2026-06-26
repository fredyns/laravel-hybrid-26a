# Async

**Source URL:** https://alpinejs.dev/advanced/async

## Overview

Alpine is built to support asynchronous functions in most places it supports standard ones.

---

## Synchronous Example

For example, let's say you have a simple function called `getLabel()` that you use as the input to an `x-text` directive:

```javascript
function getLabel() {
    return 'Hello World!'
}
```

```html
<span x-text="getLabel()"></span>
```

Because `getLabel` is synchronous, everything works as expected.

---

## Asynchronous Example

Now let's pretend that `getLabel` makes a network request to retrieve the label and can't return one instantaneously (asynchronous). By making `getLabel` an async function, you can call it from Alpine using JavaScript's `await` syntax.

```javascript
async function getLabel() {
    let response = await fetch('/api/label')
 
    return await response.text()
}
```

```html
<span x-text="await getLabel()"></span>
```

---

## Implicit Async Handling

Additionally, if you prefer calling methods in Alpine without the trailing parenthesis, you can leave them out and Alpine will detect that the provided function is async and handle it accordingly. For example:

```html
<span x-text="getLabel"></span>
```

---

## See Also

- [Advanced](../alpinejs-doc.md) — Advanced Alpine topics
- [Extending](./extending.md) — Extending Alpine
- [Reactivity](./reactivity.md) — Understanding reactivity
