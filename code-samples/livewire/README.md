# LIVEWIRE DOCUMENTATION
 
source: https://livewire.laravel.com/docs/4.x/
 
Livewire allows you to build dynamic, reactive interfaces using only PHP—no JavaScript required. Instead of writing frontend code in JavaScript frameworks, you write simple PHP classes and Blade templates, and Livewire handles all the complex JavaScript behind the scenes. To demonstrate, we'll build a simple post creation form with real-time validation. You'll see how Livewire can validate inputs and update the page dynamically without writing a single line of JavaScript or manually handling AJAX requests.


# Table Of Content

| Documentation | Local | Source |
| --- | --- | --- |
| **Essentials** | | |
| Components | [essentials/components.md](./essentials/components.md) | [https://livewire.laravel.com/docs/4.x/components](https://livewire.laravel.com/docs/4.x/components) |
| Pages | [essentials/pages.md](./essentials/pages.md) | [https://livewire.laravel.com/docs/4.x/pages](https://livewire.laravel.com/docs/4.x/pages) |
| Properties | [essentials/properties.md](./essentials/properties.md) | [https://livewire.laravel.com/docs/4.x/properties](https://livewire.laravel.com/docs/4.x/properties) |
| Actions | [essentials/actions.md](./essentials/actions.md) | [https://livewire.laravel.com/docs/4.x/actions](https://livewire.laravel.com/docs/4.x/actions) |
| Forms | [essentials/forms.md](./essentials/forms.md) | [https://livewire.laravel.com/docs/4.x/forms](https://livewire.laravel.com/docs/4.x/forms) |
| Events | [essentials/events.md](./essentials/events.md) | [https://livewire.laravel.com/docs/4.x/events](https://livewire.laravel.com/docs/4.x/events) |
| Lifecycle Hooks | [essentials/lifecycle-hooks.md](./essentials/lifecycle-hooks.md) | [https://livewire.laravel.com/docs/4.x/lifecycle-hooks](https://livewire.laravel.com/docs/4.x/lifecycle-hooks) |
| Nesting Components | [essentials/nesting.md](./essentials/nesting.md) | [https://livewire.laravel.com/docs/4.x/nesting](https://livewire.laravel.com/docs/4.x/nesting) |
| Testing | [essentials/testing.md](./essentials/testing.md) | [https://livewire.laravel.com/docs/4.x/testing](https://livewire.laravel.com/docs/4.x/testing) |
| **Features** | | |
| Alpine | [features/alpine.md](./features/alpine.md) | [https://livewire.laravel.com/docs/4.x/alpine](https://livewire.laravel.com/docs/4.x/alpine) |
| Styles | [features/styles.md](./features/styles.md) | [https://livewire.laravel.com/docs/4.x/styles](https://livewire.laravel.com/docs/4.x/styles) |
| Navigate | [features/navigate.md](./features/navigate.md) | [https://livewire.laravel.com/docs/4.x/navigate](https://livewire.laravel.com/docs/4.x/navigate) |
| Islands | [features/islands.md](./features/islands.md) | [https://livewire.laravel.com/docs/4.x/islands](https://livewire.laravel.com/docs/4.x/islands) |
| Lazy Loading | [features/lazy.md](./features/lazy.md) | [https://livewire.laravel.com/docs/4.x/lazy](https://livewire.laravel.com/docs/4.x/lazy) |
| Loading States | [features/loading-states.md](./features/loading-states.md) | [https://livewire.laravel.com/docs/4.x/loading-states](https://livewire.laravel.com/docs/4.x/loading-states) |
| Validation | [features/validation.md](./features/validation.md) | [https://livewire.laravel.com/docs/4.x/validation](https://livewire.laravel.com/docs/4.x/validation) |
| File Uploads | [features/uploads.md](./features/uploads.md) | [https://livewire.laravel.com/docs/4.x/uploads](https://livewire.laravel.com/docs/4.x/uploads) |
| Pagination | [features/pagination.md](./features/pagination.md) | [https://livewire.laravel.com/docs/4.x/pagination](https://livewire.laravel.com/docs/4.x/pagination) |
| URL Query Parameters | [features/url.md](./features/url.md) | [https://livewire.laravel.com/docs/4.x/url](https://livewire.laravel.com/docs/4.x/url) |
| Computed Properties | [features/computed-properties.md](./features/computed-properties.md) | [https://livewire.laravel.com/docs/4.x/computed-properties](https://livewire.laravel.com/docs/4.x/computed-properties) |
| Redirecting | [features/redirects.md](./features/redirects.md) | [https://livewire.laravel.com/docs/4.x/redirecting](https://livewire.laravel.com/docs/4.x/redirecting) |
| File Downloads | [features/downloads.md](./features/downloads.md) | [https://livewire.laravel.com/docs/4.x/downloads](https://livewire.laravel.com/docs/4.x/downloads) |
| Teleport | [features/teleport.md](./features/teleport.md) | [https://livewire.laravel.com/docs/4.x/teleport](https://livewire.laravel.com/docs/4.x/teleport) |
| **HTML Directives** | | |
| wire:bind | [html-directives/wire-bind.md](./html-directives/wire-bind.md) | [https://livewire.laravel.com/docs/4.x/wire-bind](https://livewire.laravel.com/docs/4.x/wire-bind) |
| wire:click | [html-directives/wire-click.md](./html-directives/wire-click.md) | [https://livewire.laravel.com/docs/4.x/wire-click](https://livewire.laravel.com/docs/4.x/wire-click) |
| wire:submit | [html-directives/wire-submit.md](./html-directives/wire-submit.md) | [https://livewire.laravel.com/docs/4.x/wire-submit](https://livewire.laravel.com/docs/4.x/wire-submit) |
| wire:model | [html-directives/wire-model.md](./html-directives/wire-model.md) | [https://livewire.laravel.com/docs/4.x/wire-model](https://livewire.laravel.com/docs/4.x/wire-model) |
| wire:loading | [html-directives/wire-loading.md](./html-directives/wire-loading.md) | [https://livewire.laravel.com/docs/4.x/wire-loading](https://livewire.laravel.com/docs/4.x/wire-loading) |
| wire:navigate | [html-directives/wire-navigate.md](./html-directives/wire-navigate.md) | [https://livewire.laravel.com/docs/4.x/wire-navigate](https://livewire.laravel.com/docs/4.x/wire-navigate) |
| wire:current | [html-directives/wire-current.md](./html-directives/wire-current.md) | [https://livewire.laravel.com/docs/4.x/wire-current](https://livewire.laravel.com/docs/4.x/wire-current) |
| wire:cloak | [html-directives/wire-cloak.md](./html-directives/wire-cloak.md) | [https://livewire.laravel.com/docs/4.x/wire-cloak](https://livewire.laravel.com/docs/4.x/wire-cloak) |
| wire:dirty | [html-directives/wire-dirty.md](./html-directives/wire-dirty.md) | [https://livewire.laravel.com/docs/4.x/wire-dirty](https://livewire.laravel.com/docs/4.x/wire-dirty) |
| wire:confirm | [html-directives/wire-confirm.md](./html-directives/wire-confirm.md) | [https://livewire.laravel.com/docs/4.x/wire-confirm](https://livewire.laravel.com/docs/4.x/wire-confirm) |
| wire:transition | [html-directives/wire-transition.md](./html-directives/wire-transition.md) | [https://livewire.laravel.com/docs/4.x/wire-transition](https://livewire.laravel.com/docs/4.x/wire-transition) |
| wire:init | [html-directives/wire-init.md](./html-directives/wire-init.md) | [https://livewire.laravel.com/docs/4.x/wire-init](https://livewire.laravel.com/docs/4.x/wire-init) |
| wire:intersect | [html-directives/wire-intersect.md](./html-directives/wire-intersect.md) | [https://livewire.laravel.com/docs/4.x/wire-intersect](https://livewire.laravel.com/docs/4.x/wire-intersect) |
| wire:poll | [html-directives/wire-poll.md](./html-directives/wire-poll.md) | [https://livewire.laravel.com/docs/4.x/wire-poll](https://livewire.laravel.com/docs/4.x/wire-poll) |
| wire:offline | [html-directives/wire-offline.md](./html-directives/wire-offline.md) | [https://livewire.laravel.com/docs/4.x/wire-offline](https://livewire.laravel.com/docs/4.x/wire-offline) |
| wire:ignore | [html-directives/wire-ignore.md](./html-directives/wire-ignore.md) | [https://livewire.laravel.com/docs/4.x/wire-ignore](https://livewire.laravel.com/docs/4.x/wire-ignore) |
| wire:ref | [html-directives/wire-ref.md](./html-directives/wire-ref.md) | [https://livewire.laravel.com/docs/4.x/wire-ref](https://livewire.laravel.com/docs/4.x/wire-ref) |
| wire:replace | [html-directives/wire-replace.md](./html-directives/wire-replace.md) | [https://livewire.laravel.com/docs/4.x/wire-replace](https://livewire.laravel.com/docs/4.x/wire-replace) |
| wire:show | [html-directives/wire-show.md](./html-directives/wire-show.md) | [https://livewire.laravel.com/docs/4.x/wire-show](https://livewire.laravel.com/docs/4.x/wire-show) |
| wire:sort | [html-directives/wire-sort.md](./html-directives/wire-sort.md) | [https://livewire.laravel.com/docs/4.x/wire-sort](https://livewire.laravel.com/docs/4.x/wire-sort) |
| wire:stream | [html-directives/wire-stream.md](./html-directives/wire-stream.md) | [https://livewire.laravel.com/docs/4.x/wire-stream](https://livewire.laravel.com/docs/4.x/wire-stream) |
| wire:text | [html-directives/wire-text.md](./html-directives/wire-text.md) | [https://livewire.laravel.com/docs/4.x/wire-text](https://livewire.laravel.com/docs/4.x/wire-text) |
| **PHP Attributes** | | |
| Async | [php-attributes/async.md](./php-attributes/async.md) | [https://livewire.laravel.com/docs/4.x/attribute-async](https://livewire.laravel.com/docs/4.x/attribute-async) |
| Computed | [php-attributes/computed.md](./php-attributes/computed.md) | [https://livewire.laravel.com/docs/4.x/attribute-computed](https://livewire.laravel.com/docs/4.x/attribute-computed) |
| Defer | [php-attributes/defer.md](./php-attributes/defer.md) | [https://livewire.laravel.com/docs/4.x/attribute-defer](https://livewire.laravel.com/docs/4.x/attribute-defer) |
| Isolate | [php-attributes/isolate.md](./php-attributes/isolate.md) | [https://livewire.laravel.com/docs/4.x/attribute-isolate](https://livewire.laravel.com/docs/4.x/attribute-isolate) |
| Js | [php-attributes/js.md](./php-attributes/js.md) | [https://livewire.laravel.com/docs/4.x/attribute-js](https://livewire.laravel.com/docs/4.x/attribute-js) |
| Json | [php-attributes/json.md](./php-attributes/json.md) | [https://livewire.laravel.com/docs/4.x/attribute-json](https://livewire.laravel.com/docs/4.x/attribute-json) |
| Layout | [php-attributes/layout.md](./php-attributes/layout.md) | [https://livewire.laravel.com/docs/4.x/attribute-layout](https://livewire.laravel.com/docs/4.x/attribute-layout) |
| Lazy | [php-attributes/lazy.md](./php-attributes/lazy.md) | [https://livewire.laravel.com/docs/4.x/attribute-lazy](https://livewire.laravel.com/docs/4.x/attribute-lazy) |
| Locked | [php-attributes/locked.md](./php-attributes/locked.md) | [https://livewire.laravel.com/docs/4.x/attribute-locked](https://livewire.laravel.com/docs/4.x/attribute-locked) |
| Modelable | [php-attributes/modelable.md](./php-attributes/modelable.md) | [https://livewire.laravel.com/docs/4.x/attribute-modelable](https://livewire.laravel.com/docs/4.x/attribute-modelable) |
| On | [php-attributes/on.md](./php-attributes/on.md) | [https://livewire.laravel.com/docs/4.x/attribute-on](https://livewire.laravel.com/docs/4.x/attribute-on) |
| Reactive | [php-attributes/reactive.md](./php-attributes/reactive.md) | [https://livewire.laravel.com/docs/4.x/attribute-reactive](https://livewire.laravel.com/docs/4.x/attribute-reactive) |
| Renderless | [php-attributes/renderless.md](./php-attributes/renderless.md) | [https://livewire.laravel.com/docs/4.x/attribute-renderless](https://livewire.laravel.com/docs/4.x/attribute-renderless) |
| Session | [php-attributes/session.md](./php-attributes/session.md) | [https://livewire.laravel.com/docs/4.x/attribute-session](https://livewire.laravel.com/docs/4.x/attribute-session) |
| Title | [php-attributes/title.md](./php-attributes/title.md) | [https://livewire.laravel.com/docs/4.x/attribute-title](https://livewire.laravel.com/docs/4.x/attribute-title) |
| Transition | [php-attributes/transition.md](./php-attributes/transition.md) | [https://livewire.laravel.com/docs/4.x/attribute-transition](https://livewire.laravel.com/docs/4.x/attribute-transition) |
| Url | [php-attributes/url.md](./php-attributes/url.md) | [https://livewire.laravel.com/docs/4.x/attribute-url](https://livewire.laravel.com/docs/4.x/attribute-url) |
| Validate | [php-attributes/validate.md](./php-attributes/validate.md) | [https://livewire.laravel.com/docs/4.x/attribute-validate](https://livewire.laravel.com/docs/4.x/attribute-validate) |
| **Blade Directives** | | |
| @island | [blade-directives/island.md](./blade-directives/island.md) | [https://livewire.laravel.com/docs/4.x/directive-island](https://livewire.laravel.com/docs/4.x/directive-island) |
| @placeholder | [blade-directives/placeholder.md](./blade-directives/placeholder.md) | [https://livewire.laravel.com/docs/4.x/directive-placeholder](https://livewire.laravel.com/docs/4.x/directive-placeholder) |
| @persist | [blade-directives/persist.md](./blade-directives/persist.md) | [https://livewire.laravel.com/docs/4.x/directive-persist](https://livewire.laravel.com/docs/4.x/directive-persist) |
| @teleport | [blade-directives/teleport.md](./blade-directives/teleport.md) | [https://livewire.laravel.com/docs/4.x/directive-teleport](https://livewire.laravel.com/docs/4.x/directive-teleport) |
| **Advanced** | | |
| Morphing | [advanced/morphing.md](./advanced/morphing.md) | [https://livewire.laravel.com/docs/4.x/morphing](https://livewire.laravel.com/docs/4.x/morphing) |
| Hydration | [advanced/hydration.md](./advanced/hydration.md) | [https://livewire.laravel.com/docs/4.x/hydration](https://livewire.laravel.com/docs/4.x/hydration) |
| Nesting | [advanced/nesting.md](./advanced/nesting.md) | [https://livewire.laravel.com/docs/4.x/understanding-nesting](https://livewire.laravel.com/docs/4.x/understanding-nesting) |
| Troubleshooting | [advanced/troubleshooting.md](./advanced/troubleshooting.md) | [https://livewire.laravel.com/docs/4.x/troubleshooting](https://livewire.laravel.com/docs/4.x/troubleshooting) |
| Security | [advanced/security.md](./advanced/security.md) | [https://livewire.laravel.com/docs/4.x/security](https://livewire.laravel.com/docs/4.x/security) |
| CSP | [advanced/csp.md](./advanced/csp.md) | [https://livewire.laravel.com/docs/4.x/csp](https://livewire.laravel.com/docs/4.x/csp) |
| JavaScript | [advanced/javascript.md](./advanced/javascript.md) | [https://livewire.laravel.com/docs/4.x/javascript](https://livewire.laravel.com/docs/4.x/javascript) |
| Synthesizers | [advanced/synthesizers.md](./advanced/synthesizers.md) | [https://livewire.laravel.com/docs/4.x/synthesizers](https://livewire.laravel.com/docs/4.x/synthesizers) |
| Contribution Guide | [advanced/contribution-guide.md](./advanced/contribution-guide.md) | [https://livewire.laravel.com/docs/4.x/contribution-guide](https://livewire.laravel.com/docs/4.x/contribution-guide) |
