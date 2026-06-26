# $watch

**Source URL:** https://alpinejs.dev/magics/watch

## Overview

You can "watch" a component property using the `$watch` magic method. For example:

```html
<div x-data="{ open: false }" x-init="$watch('open', value => console.log(value))">
    <button @click="open = ! open">Toggle Open</button>
</div>
```

In the above example, when the button is pressed and `open` is changed, the provided callback will fire and console.log the new value.

---

## Watching Nested Properties

You can watch deeply nested properties using "dot" notation:

```html
<div x-data="{ foo: { bar: 'baz' }}" x-init="$watch('foo.bar', value => console.log(value))">
    <button @click="foo.bar = 'bob'">Toggle Open</button>
</div>
```

When the `<button>` is pressed, `foo.bar` will be set to "bob", and "bob" will be logged to the console.

---

## Getting the "Old" Value

`$watch` keeps track of the previous value of the property being watched. You can access it using the optional second argument to the callback like so:

```html
<div x-data="{ open: false }" x-init="$watch('open', (value, oldValue) => console.log(value, oldValue))">
    <button @click="open = ! open">Toggle Open</button>
</div>
```

---

## Deep Watching

`$watch` automatically watches for changes at any level but you should keep in mind that, when a change is detected, the watcher will return the value of the observed property, not the value of the subproperty that has changed.

```html
<div x-data="{ foo: { bar: 'baz' }}" x-init="$watch('foo', (value, oldValue) => console.log(value, oldValue))">
    <button @click="foo.bar = 'bob'">Update</button>
</div>
```

When the `<button>` is pressed, `foo.bar` will be set to "bob", and `{bar: 'bob'} {bar: 'baz'}` will be logged to the console (new and old value).

### Warning: Infinite Loops

> ⚠️ Changing a property of a "watched" object as a side effect of the `$watch` callback will generate an infinite loop and eventually error.

```html
<!-- 🚫 Infinite loop -->
<div x-data="{ foo: { bar: 'baz', bob: 'lob' }}" x-init="$watch('foo', value => foo.bob = foo.bar)">
    <button @click="foo.bar = 'bob'">Update</button>
</div>
```

---

## See Also

- [x-effect](../directives/effect.md) — Reactive effects
- [Lifecycle](../essentials/lifecycle.md) — Component lifecycle
- [State](../essentials/state.md) — Managing component state
