# Alpine.js Documentation

**Version:** 3.x  
**Official Documentation:** https://alpinejs.dev/

## Overview

Alpine.js is a rugged, minimal framework for composing JavaScript behavior in your markup. It offers you the reactive and declarative nature of big frameworks like Vue or React at a much lower cost. You get to keep your DOM, and sprinkle in behavior as you see fit.

Think of it like Tailwind for JavaScript — a set of low-level utility directives for adding interactivity to your HTML without leaving your markup.

---

## Table of Contents

### Getting Started
| Topic | File | URL |
|-------|------|-----|
| Start Here | [start-here.md](./start-here.md) | https://alpinejs.dev/start-here |

### Essentials
| Topic | File | URL |
|-------|------|-----|
| State | [essentials/state.md](./essentials/state.md) | https://alpinejs.dev/essentials/state |
| Templating | [essentials/templating.md](./essentials/templating.md) | https://alpinejs.dev/essentials/templating |
| Events | [essentials/events.md](./essentials/events.md) | https://alpinejs.dev/essentials/events |
| Lifecycle | [essentials/lifecycle.md](./essentials/lifecycle.md) | https://alpinejs.dev/essentials/lifecycle |

### Directives
| Directive | File | URL |
|-----------|------|-----|
| x-data | [directives/data.md](./directives/data.md) | https://alpinejs.dev/directives/data |
| x-init | [directives/init.md](./directives/init.md) | https://alpinejs.dev/directives/init |
| x-show | [directives/show.md](./directives/show.md) | https://alpinejs.dev/directives/show |
| x-bind | [directives/bind.md](./directives/bind.md) | https://alpinejs.dev/directives/bind |
| x-on | [directives/on.md](./directives/on.md) | https://alpinejs.dev/directives/on |
| x-text | [directives/text.md](./directives/text.md) | https://alpinejs.dev/directives/text |
| x-html | [directives/html.md](./directives/html.md) | https://alpinejs.dev/directives/html |
| x-model | [directives/model.md](./directives/model.md) | https://alpinejs.dev/directives/model |
| x-modelable | [directives/modelable.md](./directives/modelable.md) | https://alpinejs.dev/directives/modelable |
| x-for | [directives/for.md](./directives/for.md) | https://alpinejs.dev/directives/for |
| x-transition | [directives/transition.md](./directives/transition.md) | https://alpinejs.dev/directives/transition |
| x-effect | [directives/effect.md](./directives/effect.md) | https://alpinejs.dev/directives/effect |
| x-ignore | [directives/ignore.md](./directives/ignore.md) | https://alpinejs.dev/directives/ignore |
| x-ref | [directives/ref.md](./directives/ref.md) | https://alpinejs.dev/directives/ref |
| x-cloak | [directives/cloak.md](./directives/cloak.md) | https://alpinejs.dev/directives/cloak |
| x-teleport | [directives/teleport.md](./directives/teleport.md) | https://alpinejs.dev/directives/teleport |
| x-if | [directives/if.md](./directives/if.md) | https://alpinejs.dev/directives/if |
| x-id | [directives/id.md](./directives/id.md) | https://alpinejs.dev/directives/id |

### Magics
| Magic | File | URL |
|-------|------|-----|
| $el | [magics/el.md](./magics/el.md) | https://alpinejs.dev/magics/el |
| $watch | [magics/watch.md](./magics/watch.md) | https://alpinejs.dev/magics/watch |
| $watchEffect | [magics/watch-effect.md](./magics/watch-effect.md) | https://alpinejs.dev/magics/watch-effect |
| $dispatch | [magics/dispatch.md](./magics/dispatch.md) | https://alpinejs.dev/magics/dispatch |
| $nextTick | [magics/next-tick.md](./magics/next-tick.md) | https://alpinejs.dev/magics/next-tick |
| $id | [magics/id.md](./magics/id.md) | https://alpinejs.dev/magics/id |

### Global Properties
| Property                | File                                                             | URL                                                  |
|-------------------------|------------------------------------------------------------------|------------------------------------------------------|
| Alpine.data()           | [globals/data.md](./globals/data.md)                             | https://alpinejs.dev/globals/alpine-data             |
| Alpine.store()          | [globals/store.md](./globals/store.md)                           | https://alpinejs.dev/globals/alpine-store            |
| Alpine.bind()           | [globals/bind.md](./globals/bind.md)                             | https://alpinejs.dev/globals/alpine-bind             |

### Plugins
| Plugin | File | URL |
|--------|------|-----|
| Mask | [plugins/mask.md](./plugins/mask.md) | https://alpinejs.dev/plugins/mask |
| Intersect | [plugins/intersect.md](./plugins/intersect.md) | https://alpinejs.dev/plugins/intersect |
| Resize | [plugins/resize.md](./plugins/resize.md) | https://alpinejs.dev/plugins/resize |
| Persist | [plugins/persist.md](./plugins/persist.md) | https://alpinejs.dev/plugins/persist |
| Focus | [plugins/focus.md](./plugins/focus.md) | https://alpinejs.dev/plugins/focus |
| Collapse | [plugins/collapse.md](./plugins/collapse.md) | https://alpinejs.dev/plugins/collapse |
| Anchor | [plugins/anchor.md](./plugins/anchor.md) | https://alpinejs.dev/plugins/anchor |
| Morph | [plugins/morph.md](./plugins/morph.md) | https://alpinejs.dev/plugins/morph |
| Sort | [plugins/sort.md](./plugins/sort.md) | https://alpinejs.dev/plugins/sort |

### Advanced
| Topic | File | URL |
|-------|------|-----|
| CSP | [advanced/csp.md](./advanced/csp.md) | https://alpinejs.dev/advanced/csp |
| Reactivity | [advanced/reactivity.md](./advanced/reactivity.md) | https://alpinejs.dev/advanced/reactivity |
| Extending | [advanced/extending.md](./advanced/extending.md) | https://alpinejs.dev/advanced/extending |
| Async | [advanced/async.md](./advanced/async.md) | https://alpinejs.dev/advanced/async |

---

## Quick Start

Alpine.js is a JavaScript framework that lets you add interactivity to your HTML with minimal JavaScript. Here's a simple example:

```html
<div x-data="{ count: 0 }">
    <button x-on:click="count++">Increment</button>
    <span x-text="count"></span>
</div>
```

## Installation

Include Alpine.js via CDN:

```html
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
```

Or install via npm:

```bash
npm install alpinejs
```

## Key Concepts

- **Directives:** HTML attributes that add behavior (e.g., `x-data`, `x-on`, `x-model`)
- **Magics:** Special properties that provide utilities (e.g., `$el`, `$watch`, `$dispatch`)
- **Reactivity:** Automatic updates when data changes
- **Plugins:** Optional extensions for additional functionality

## Learning Path

1. Start with [Installation](./essentials/installation.md)
2. Learn [State Management](./essentials/state.md)
3. Explore [Directives](./directives/data.md)
4. Master [Events](./essentials/events.md)
5. Dive into [Advanced Topics](./advanced/reactivity.md)

---

## Resources

- **Official Website:** https://alpinejs.dev/
- **GitHub Repository:** https://github.com/alpinejs/alpine
- **Discord Community:** https://alpinejs.codewithhugo.com/chat/
- **Twitter:** https://twitter.com/Alpine_JS

---

## Notes

This documentation is a comprehensive guide to Alpine.js 3.x. Each section includes detailed explanations, code examples, and best practices for using Alpine.js in your projects.

All documentation is extracted from the official Alpine.js website and organized for easy reference and learning.
