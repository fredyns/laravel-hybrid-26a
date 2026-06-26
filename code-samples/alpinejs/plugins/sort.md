# Sort Plugin

**Source URL:** https://alpinejs.dev/plugins/sort

## Overview

Alpine's Sort plugin allows you to easily re-order elements by dragging them with your mouse.

This functionality is useful for things like Kanban boards, to-do lists, sortable table columns, etc.

The drag functionality used in this plugin is provided by the SortableJS project.

---

## Installation

You can use this plugin by either including it from a `<script>` tag or installing it via NPM:

### Via CDN

You can include the CDN build of this plugin as a `<script>` tag; just make sure to include it BEFORE Alpine's core JS file.

```html
<!-- Alpine Plugins -->
<script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/sort@3.x.x/dist/cdn.min.js"></script>
 
<!-- Alpine Core -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
```

### Via NPM

You can install Sort from NPM for use inside your bundle like so:

```bash
npm install @alpinejs/sort
```

Then initialize it from your bundle:

```javascript
import Alpine from 'alpinejs'
import sort from '@alpinejs/sort'
 
Alpine.plugin(sort)
```

---

## Basic Usage

The primary API for using this plugin is the `x-sort` directive. By adding `x-sort` to an element, its children containing `x-sort:item` become sortable—meaning you can drag them around with your mouse, and they will change positions.

```html
<ul x-sort>
    <li x-sort:item>foo</li>
    <li x-sort:item>bar</li>
    <li x-sort:item>baz</li>
</ul>
```

---

## Sort Handlers

You can react to sorting changes by passing a handler function to `x-sort` and adding keys to each item using `x-sort:item`. Here is an example of a simple handler function that shows an alert dialog with the changed item's key and its new position:

```html
<ul x-sort="alert($item + ' - ' + $position)">
    <li x-sort:item="1">foo</li>
    <li x-sort:item="2">bar</li>
    <li x-sort:item="3">baz</li>
</ul>
```

The `x-sort` handler will be called every time the sort order of the items change. The `$item` magic will contain the key of the sorted element (derived from `x-sort:item`), and `$position` will contain the new position of the item (starting at index 0).

You can also pass a handler function to `x-sort` and that function will receive the item and position as the first and second parameter:

```html
<div x-data="{ handle: (item, position) => { ... } }">
    <ul x-sort="handle">
        <li x-sort:item="1">foo</li>
        <li x-sort:item="2">bar</li>
        <li x-sort:item="3">baz</li>
    </ul>
</div>
```

Handler functions are often used to persist the new order of items in the database so that the sorting order of a list is preserved between page refreshes.

---

## Sorting Groups

This plugin allows you to drag items from one `x-sort` sortable list into another one by adding a matching `x-sort:group` value to both lists:

```html
<div>
    <ul x-sort x-sort:group="todos">
        <li x-sort:item="1">foo</li>
        <li x-sort:item="2">bar</li>
        <li x-sort:item="3">baz</li>
    </ul>
 
    <ol x-sort x-sort:group="todos">
        <li x-sort:item="4">foo</li>
        <li x-sort:item="5">bar</li>
        <li x-sort:item="6">baz</li>
    </ol>
</div>
```

Because both sortable lists above use the same group name (todos), you can drag items from one list onto another.

When using sort handlers like `x-sort="handle"` and dragging an item from one group to another, only the destination list's handler will be called with the key and new position.

---

## Drag Handles

By default, each `x-sort:item` element is draggable by clicking and dragging anywhere within it. However, you may want to designate a smaller, more specific element as the "drag handle" so that the rest of the element can be interacted with like normal, and only the handle will respond to mouse dragging:

```html
<ul x-sort>
    <li x-sort:item>
        <span x-sort:handle> - </span>foo
    </li>
 
    <li x-sort:item>
        <span x-sort:handle> - </span>bar
    </li>
 
    <li x-sort:item>
        <span x-sort:handle> - </span>baz
    </li>
</ul>
```

As you can see in the above example, the hyphen "-" is draggable, but the item text ("foo") is not.

---

## Ignoring Elements

Sometimes you want to prevent certain elements within a sortable item from initiating a drag operation. This is especially useful when you have interactive elements like buttons, dropdowns, or links that users should be able to click without accidentally dragging the sortable item.

You can use the `x-sort:ignore` directive to mark elements that should not trigger dragging:

```html
<ul x-sort>
    <li x-sort:item>
        <!-- ... -->
 
        <button x-sort:ignore>Edit</button>
    </li>
 
    <li x-sort:item>
        <!-- ... -->
 
        <button x-sort:ignore>Edit</button>
    </li>
 
    <li x-sort:item>
        <!-- ... -->
 
        <button x-sort:ignore>Edit</button>
    </li>
</ul>
```

In the above example, users can click and drag the item itself, but clicking on the "Edit" button will not initiate a drag operation.

> **Note:** Elements with `x-sort:ignore` will still function normally (buttons can be clicked, inputs can be focused, etc.) - they are only excluded from drag operations.

---

## Ghost Elements

When a user drags an item, the element will follow their mouse to appear as though they are physically dragging the element.

By default, a "hole" (empty space) will be left in the original element's place during the drag.

If you would like to show a "ghost" of the original element in its place instead of an empty space, you can add the `.ghost` modifier to `x-sort`:

```html
<ul x-sort.ghost>
    <li x-sort:item>foo</li>
    <li x-sort:item>bar</li>
    <li x-sort:item>baz</li>
</ul>
```

---

## Styling the Ghost Element

By default, the "ghost" element has a `.sortable-ghost` CSS class attached to it while the original element is being dragged.

This makes it easy to add any custom styling you would like:

```html
<style>
.sortable-ghost {
    opacity: .5 !important;
}
</style>
 
<ul x-sort.ghost>
    <li x-sort:item>foo</li>
    <li x-sort:item>bar</li>
    <li x-sort:item>baz</li>
</ul>
```

---

## Sorting Class on Body

While an element is being dragged around, Alpine will automatically add a `.sorting` class to the `<body>` element of the page.

This is useful for styling any element on the page conditionally using only CSS.

For example you could have a warning that only displays while a user is sorting items:

```html
<div id="sort-warning">
    Page functionality is limited while sorting
</div>
```

To show this only while sorting, you can use the `body.sorting` CSS selector:

```css
#sort-warning {
    display: none;
}
 
body.sorting #sort-warning {
    display: block;
}
```

---

## CSS Hover Bug

Currently, there is a bug in Chrome and Safari (not Firefox) that causes issues with hover styles.

If you drag one of the elements in a list with hover effects, the hover effect will be errantly applied to any element in the original element's place.

To fix this, you can leverage the `.sorting` class applied to the body while sorting to limit the hover effect to only be applied while `.sorting` does NOT exist on body.

Here is how you can do this directly inline using Tailwind arbitrary variants:

```html
<div x-sort>
    <div x-sort:item class="[body:not(.sorting)_&]:hover:border">foo</div>
    <div x-sort:item class="[body:not(.sorting)_&]:hover:border">bar</div>
    <div x-sort:item class="[body:not(.sorting)_&]:hover:border">baz</div>
</div>
```

---

## Custom Configuration

Alpine chooses sensible defaults for configuring SortableJS under the hood. However, you can add or override any of these options yourself using `x-sort:config`:

```html
<ul x-sort x-sort:config="{ animation: 0 }">
    <li x-sort:item>foo</li>
    <li x-sort:item>bar</li>
    <li x-sort:item>baz</li>
</ul>
```

Any config options passed will overwrite Alpine defaults. In this case of animation, this is fine, however be aware that overwriting handle, group, filter, onSort, onStart, or onEnd may break functionality.

---

## See Also

- [Plugins](../alpinejs-doc.md) — Alpine plugins overview
- [x-sort:item](../directives/for.md) — Sortable items
