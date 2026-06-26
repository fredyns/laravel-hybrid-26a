# x-ref

**Source URL:** https://alpinejs.dev/directives/ref

## Overview

`x-ref` in combination with `$refs` is a useful utility for easily accessing DOM elements directly. It's most useful as a replacement for APIs like `getElementById` and `querySelector`.

```html
<button @click="$refs.text.remove()">Remove Text</button>
 
<span x-ref="text">Hello 👋</span>
```

---

## See Also

- [$refs](../magics/refs.md) — Access DOM elements
- [x-data](./data.md) — Declare reactive data
