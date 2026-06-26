# Focus Plugin

**Source URL:** https://alpinejs.dev/plugins/focus

## Overview

Alpine's Focus plugin allows you to manage focus on a page.

This plugin internally makes heavy use of the open source tool: Tabbable. Big thanks to that team for providing a much needed solution to this problem.

> **Note:** This Plugin was previously called "Trap". Trap's functionality has been absorbed into this plugin along with additional functionality. You can swap Trap for Focus without any breaking changes.

---

## Installation

You can use this plugin by either including it from a `<script>` tag or installing it via NPM:

### Via CDN

You can include the CDN build of this plugin as a `<script>` tag, just make sure to include it BEFORE Alpine's core JS file.

```html
<!-- Alpine Plugins -->
<script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/focus@3.x.x/dist/cdn.min.js"></script>
 
<!-- Alpine Core -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
```

### Via NPM

You can install Focus from NPM for use inside your bundle like so:

```bash
npm install @alpinejs/focus
```

Then initialize it from your bundle:

```javascript
import Alpine from 'alpinejs'
import focus from '@alpinejs/focus'
 
Alpine.plugin(focus)
```

---

## x-trap

Focus offers a dedicated API for trapping focus within an element: the `x-trap` directive.

`x-trap` accepts a JS expression. If the result of that expression is true, then the focus will be trapped inside that element until the expression becomes false, then at that point, focus will be returned to where it was previously.

For example:

```html
<div x-data="{ open: false }">
    <button @click="open = true">Open Dialog</button>
 
    <span x-show="open" x-trap="open">
        <p>...</p>
 
        <input type="text" placeholder="Some input...">
 
        <input type="text" placeholder="Some other input...">
 
        <button @click="open = false">Close Dialog</button>
    </span>
</div>
```

---

## Nesting Dialogs

Sometimes you may want to nest one dialog inside another. `x-trap` makes this trivial and handles it automatically.

`x-trap` keeps track of newly "trapped" elements and stores the last actively focused element. Once the element is "untrapped" then the focus will be returned to where it was originally.

This mechanism is recursive, so you can trap focus within an already trapped element infinite times, then "untrap" each element successively.

---

## Modifiers

### .inert

When building things like dialogs/modals, it's recommended to hide all the other elements on the page from screen readers when trapping focus.

By adding `.inert` to `x-trap`, when focus is trapped, all other elements on the page will receive `aria-hidden="true"` attributes, and when focus trapping is disabled, those attributes will also be removed.

```html
<body x-data="{ open: false }">
    <div x-trap.inert="open" ...>
        ...
    </div>
 
    <div>
        ...
    </div>
</body>
```

### .noscroll

When building dialogs/modals with Alpine, it's recommended that you disable scrolling for the surrounding content when the dialog is open.

`x-trap` allows you to do this automatically with the `.noscroll` modifier.

By adding `.noscroll`, Alpine will remove the scrollbar from the page and block users from scrolling down the page while a dialog is open.

```html
<div x-data="{ open: false }">
    <button @click="open = true">Open Dialog</button>
 
    <div x-show="open" x-trap.noscroll="open">
        Dialog Contents
 
        <button @click="open = false">Close Dialog</button>
    </div>
</div>
```

### .noreturn

Sometimes you may not want focus to be returned to where it was previously. Consider a dropdown that's triggered upon focusing an input, returning focus to the input on close will just trigger the dropdown to open again.

`x-trap` allows you to disable this behavior with the `.noreturn` modifier.

By adding `.noreturn`, Alpine will not return focus upon `x-trap` evaluating to false.

### .noautofocus

By default, when `x-trap` traps focus within an element, it focuses the first focussable element within that element. This is a sensible default, however there are times where you may want to disable this behavior and not automatically focus any elements when `x-trap` engages.

By adding `.noautofocus`, Alpine will not automatically focus any elements when trapping focus.

---

## $focus

This plugin offers many smaller utilities for managing focus within a page. These utilities are exposed via the `$focus` magic.

| Property | Description |
|----------|-------------|
| `focus(el)` | Focus the passed element (handling annoyances internally: using nextTick, etc.) |
| `focusable(el)` | Detect whether or not an element is focusable |
| `focusables()` | Get all "focusable" elements within the current element |
| `focused()` | Get the currently focused element on the page |
| `lastFocused()` | Get the last focused element on the page |
| `within(el)` | Specify an element to scope the $focus magic to (the current element by default) |
| `first()` | Focus the first focusable element |
| `last()` | Focus the last focusable element |
| `next()` | Focus the next focusable element |
| `previous()` | Focus the previous focusable element |
| `noscroll()` | Prevent scrolling to the element about to be focused |
| `wrap()` | When retrieving "next" or "previous" use "wrap around" (ex. returning the first element if getting the "next" element of the last element) |
| `getFirst()` | Retrieve the first focusable element |
| `getLast()` | Retrieve the last focusable element |
| `getNext()` | Retrieve the next focusable element |
| `getPrevious()` | Retrieve the previous focusable element |

### Example: Arrow Key Navigation

The example below allows the user to control focus within the group of buttons using the arrow keys:

```html
<div
    @keydown.right="$focus.next()"
    @keydown.left="$focus.previous()"
>
    <button>First</button>
    <button>Second</button>
    <button>Third</button>
</div>
```

### Example: Wrap Around

To enable wrap-around behavior where focus cycles back to the first element:

```html
<div
    @keydown.right="$focus.wrap().next()"
    @keydown.left="$focus.wrap().previous()"
>
    <button>First</button>
    <button>Second</button>
    <button>Third</button>
</div>
```

### Example: Scoped Focus Management

To focus the first or last element in a specific group:

```html
<button @click="$focus.within($refs.buttons).first()">Focus "First"</button>
<button @click="$focus.within($refs.buttons).last()">Focus "Last"</button>
 
<div
    x-ref="buttons"
    @keydown.right="$focus.wrap().next()"
    @keydown.left="$focus.wrap().previous()"
>
    <button>First</button>
    <button>Second</button>
    <button>Third</button>
</div>
```

---

## See Also

- [Plugins](../alpinejs-doc.md) — Alpine plugins overview
- [x-trap](../directives/teleport.md) — Focus trapping
