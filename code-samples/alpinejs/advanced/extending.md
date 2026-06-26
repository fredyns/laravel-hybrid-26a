# Extending

**Source URL:** https://alpinejs.dev/advanced/extending

## Overview

Alpine has a very open codebase that allows for extension in a number of ways. In fact, every available directive and magic in Alpine itself uses these exact APIs. In theory you could rebuild all of Alpine's functionality using them yourself.

---

## Lifecycle Concerns

Before we dive into each individual API, let's first talk about where in your codebase you should consume these APIs.

Because these APIs have an impact on how Alpine initializes the page, they must be registered AFTER Alpine is downloaded and available on the page, but BEFORE it has initialized the page itself.

### Via a Script Tag

If you are including Alpine via a script tag, you will need to register any custom extension code inside an `alpine:init` event listener.

Here's an example:

```html
<html>
    <script src="/js/alpine.js" defer></script>
 
    <div x-data x-foo></div>
 
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.directive('foo', ...)
        })
    </script>
</html>
```

If you want to extract your extension code into an external file, you will need to make sure that file's `<script>` tag is located BEFORE Alpine's like so:

```html
<html>
    <script src="/js/foo.js" defer></script>
    <script src="/js/alpine.js" defer></script>
 
    <div x-data x-foo></div>
</html>
```

### Via an NPM Module

If you imported Alpine into a bundle, you have to make sure you are registering any extension code IN BETWEEN when you import the Alpine global object, and when you initialize Alpine by calling `Alpine.start()`. For example:

```javascript
import Alpine from 'alpinejs'
 
Alpine.directive('foo', ...)
 
window.Alpine = Alpine
window.Alpine.start()
```

---

## Custom Directives

Alpine allows you to register your own custom directives using the `Alpine.directive()` API.

### Method Signature

```javascript
Alpine.directive('[name]', (el, { value, modifiers, expression }, { Alpine, effect, cleanup }) => {})
```

| Parameter | Description |
|-----------|-------------|
| `name` | The name of the directive. The name "foo" for example would be consumed as `x-foo` |
| `el` | The DOM element the directive is added to |
| `value` | If provided, the part of the directive after a colon. Ex: 'bar' in `x-foo:bar` |
| `modifiers` | An array of dot-separated trailing additions to the directive. Ex: `['baz', 'lob']` from `x-foo.baz.lob` |
| `expression` | The attribute value portion of the directive. Ex: law from `x-foo="law"` |
| `Alpine` | The Alpine global object |
| `effect` | A function to create reactive effects that will auto-cleanup after this directive is removed from the DOM |
| `cleanup` | A function you can pass bespoke callbacks to that will run when this directive is removed from the DOM |

### Simple Example

Here's an example of a simple directive called `x-uppercase`:

```javascript
Alpine.directive('uppercase', el => {
    el.textContent = el.textContent.toUpperCase()
})
```

```html
<div x-data>
    <span x-uppercase>Hello World!</span>
</div>
```

### Evaluating Expressions

When registering a custom directive, you may want to evaluate a user-supplied JavaScript expression:

```javascript
Alpine.directive('log', (el, { expression }, { evaluate }) => {
    console.log(
        evaluate(expression)
    )
})
```

### Introducing Reactivity

To make your directive reactive, use `evaluateLater()` and `effect()`:

```javascript
Alpine.directive('log', (el, { expression }, { evaluateLater, effect }) => {
    let getThingToLog = evaluateLater(expression)
 
    effect(() => {
        getThingToLog(thingToLog => {
            console.log(thingToLog)
        })
    })
})
```

### Cleaning Up

To register cleanup code when a directive is removed:

```javascript
Alpine.directive('...', (el, {}, { cleanup }) => {
    let handler = () => {}
 
    window.addEventListener('click', handler)
 
    cleanup(() => {
        window.removeEventListener('click', handler)
    })
})
```

### Custom Order

By default, custom directives run after most standard ones. Use `.before()` to change this:

```javascript
Alpine.directive('foo', (el, { value, modifiers, expression }) => {
    Alpine.addScopeToNode(el, {foo: 'bar'})
}).before('bind')
```

---

## Custom Magics

Alpine allows you to register custom "magics" (properties or methods) using `Alpine.magic()`. Any magic you register will be available to all your application's Alpine code with the `$` prefix.

### Method Signature

```javascript
Alpine.magic('[name]', (el, { Alpine }) => {})
```

| Parameter | Description |
|-----------|-------------|
| `name` | The name of the magic. The name "foo" for example would be consumed as `$foo` |
| `el` | The DOM element the magic was triggered from |
| `Alpine` | The Alpine global object |

### Magic Properties

Here's a basic example of a `$now` magic helper:

```javascript
Alpine.magic('now', () => {
    return (new Date).toLocaleTimeString()
})
```

```html
<span x-text="$now"></span>
```

### Magic Functions

To create a `$clipboard()` magic function:

```javascript
Alpine.magic('clipboard', () => {
    return subject => navigator.clipboard.writeText(subject)
})
```

```html
<button @click="$clipboard('hello world')">Copy "Hello World"</button>
```

---

## Writing and Sharing Plugins

### Script Include

For a plugin to be included via a script tag:

```html
<html>
    <script src="/js/foo.js" defer></script>
    <script src="/js/alpine.js" defer></script>
 
    <div x-data x-init="$foo()">
        <span x-foo="'hello world'">
    </div>
</html>
```

Inside `/js/foo.js`:

```javascript
document.addEventListener('alpine:init', () => {
    window.Alpine.directive('foo', ...)
 
    window.Alpine.magic('foo', ...)
})
```

### Bundle Module

For a plugin to be installed via NPM:

```javascript
import Alpine from 'alpinejs'
 
import foo from 'foo'
Alpine.plugin(foo)
 
window.Alpine = Alpine
window.Alpine.start()
```

The plugin source:

```javascript
export default function (Alpine) {
    Alpine.directive('foo', ...)
    Alpine.magic('foo', ...)
}
```

---

## See Also

- [Advanced](../alpinejs-doc.md) — Advanced Alpine topics
- [Reactivity](./reactivity.md) — Understanding reactivity
- [Alpine.plugin()](../globals/plugin.md) — Register plugins
