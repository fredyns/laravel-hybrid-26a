# $refs

**Source URL:** https://alpinejs.dev/magics/refs

## Overview

`$refs` is a magic property that can be used to retrieve DOM elements marked with `x-ref` inside the component. This is useful when you need to manually manipulate DOM elements. It's often used as a more succinct, scoped, alternative to `document.querySelector`.

```html
<button @click="$refs.text.remove()">Remove Text</button>
 
<span x-ref="text">Hello 👋</span>
```

Now, when the `<button>` is pressed, the `<span>` will be removed.

---

## Limitations

In V2 it was possible to bind `$refs` to elements dynamically, like seen below:

```html
<template x-for="item in items" :key="item.id" >
    <div :x-ref="item.name">
    some content ...
    </div>
</template>
```

However, in V3, `$refs` can only be accessed for elements that are created statically. So for the example above: if you were expecting the value of `item.name` inside of `$refs` to be something like "Batteries", you should be aware that `$refs` will actually contain the literal string 'item.name' and not "Batteries".

---

## See Also

- [$el](./el.md) — Access the current DOM node
- [x-ref](../directives/ref.md) — Reference DOM elements
