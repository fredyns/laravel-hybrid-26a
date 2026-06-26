# $el

**Source URL:** https://alpinejs.dev/magics/el

## Overview

`$el` is a magic property that can be used to retrieve the current DOM node.

```html
<button @click="$el.innerHTML = 'Hello World!'">Replace me with "Hello World!"</button>
```

---

## See Also

- [$refs](./refs.md) — Access multiple DOM elements
- [x-ref](../directives/ref.md) — Reference DOM elements
