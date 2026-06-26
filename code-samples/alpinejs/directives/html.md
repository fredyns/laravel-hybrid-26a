# x-html

**Source URL:** https://alpinejs.dev/directives/html

## Overview

`x-html` sets the "innerHTML" property of an element to the result of a given expression.

> ⚠️ **Warning:** Only use on trusted content and never on user-provided content. Dynamically rendering HTML from third parties can easily lead to XSS vulnerabilities.

Here's a basic example of using `x-html` to display a user's username.

```html
<div x-data="{ username: '<strong>calebporzio</strong>' }">
    Username: <span x-html="username"></span>
</div>
```

Now the `<span>` tag's inner HTML will be set to the HTML content.

---

## See Also

- [x-text](./text.md) — Set text content
- [Templating](../essentials/templating.md) — Inner HTML
- [x-bind](./bind.md) — Bind attributes
