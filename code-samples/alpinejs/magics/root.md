# $root

**Source URL:** https://alpinejs.dev/magics/root

## Overview

`$root` is a magic property that can be used to retrieve the root element of any Alpine component. In other words the closest element up the DOM tree that contains `x-data`.

```html
<div x-data data-message="Hello World!">
    <button @click="alert($root.dataset.message)">Say Hi</button>
</div>
```

---

## See Also

- [$el](./el.md) — Access the current DOM node
- [x-data](../directives/data.md) — Declare reactive data
