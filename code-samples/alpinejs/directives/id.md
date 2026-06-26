# x-id

**Source URL:** https://alpinejs.dev/directives/id

## Overview

`x-id` allows you to declare a new "scope" for any new IDs generated using `$id()`. It accepts an array of strings (ID names) and adds a suffix to each `$id('...')` generated within it that is unique to other IDs on the page.

`x-id` is meant to be used in conjunction with the `$id()` magic.

---

## Basic Usage

Here's a brief example of this directive in use:

```html
<div x-id="['text-input']">
    <label :for="$id('text-input')">Username</label>
    <!-- for="text-input-1" -->
 
    <input type="text" :id="$id('text-input')">
    <!-- id="text-input-1" -->
</div>
 
<div x-id="['text-input']">
    <label :for="$id('text-input')">Username</label>
    <!-- for="text-input-2" -->
 
    <input type="text" :id="$id('text-input')">
    <!-- id="text-input-2" -->
</div>
```

---

## See Also

- [$id](../magics/id.md) — Generate unique IDs
- [x-data](./data.md) — Declare reactive data
- [x-bind](./bind.md) — Bind attributes
