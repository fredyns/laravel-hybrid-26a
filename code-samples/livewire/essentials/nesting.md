# Nesting Components

**Source URL:** https://livewire.laravel.com/docs/4.x/nesting

## Overview

Livewire allows you to nest additional Livewire components inside of a parent component. This feature is immensely powerful, as it allows you to re-use and encapsulate behavior within Livewire components that are shared across your application.

**Important:** Before you extract a portion of your template into a nested Livewire component, ask yourself: Does this content in this component need to be "live"? If not, we recommend that you create a simple Blade component instead. Only create a Livewire component if the component benefits from Livewire's dynamic nature or if there is a direct performance benefit.

## Nesting a Component

To nest a Livewire component within a parent component, simply include it in the parent component's Blade view. Below is an example of a dashboard parent component that contains a nested todos component:

```php
<?php // resources/views/components/⚡dashboard.blade.php

use Livewire\Component;

new class extends Component {
    //
};
?>

<div>
    <h1>Dashboard</h1>

    <livewire:todos /> 
</div>
```

On this page's initial render, the dashboard component will encounter `<livewire:todos />` and render it in place. On a subsequent network request to dashboard, the nested todos component will skip rendering because it is now its own independent component on the page.

## Passing Props to Children

Passing data from a parent component to a child component is straightforward. In fact, it's very much like passing props to a typical Blade component.

For example, let's check out a todos component that passes a collection of `$todos` to a child component called `todo-count`:

```php
<?php // resources/views/components/⚡todos.blade.php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    #[Computed]
    public function todos()
    {
        return Auth::user()->todos,
    }
};
?>

<div>
    <livewire:todo-count :todos="$this->todos" />

    <!-- ... -->
</div>
```

As you can see, we are passing `$this->todos` into `todo-count` with the syntax: `:todos="$this->todos"`.

Now that `$todos` has been passed to the child component, you can receive that data through the child component's `mount()` method:

```php
<?php // resources/views/components/⚡todo-count.blade.php

use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\Todo;

new class extends Component {
    public $todos;

    public function mount($todos)
    {
        $this->todos = $todos;
    }

    #[Computed]
    public function count()
    {
        return $this->todos->count(),
    }
};
?>

<div>
    Count: {{ $this->count }}
</div>
```

**Tip:** You can omit the `mount()` method as a shorter alternative if the property and parameter names match:

```php
public $todos; 
```

## Passing Static Props

In the previous example, we passed props to our child component using Livewire's dynamic prop syntax, which supports PHP expressions like so:

```html
<livewire:todo-count :todos="$todos" />
```

However, sometimes you may want to pass a component a simple static value such as a string. In these cases, you may omit the colon from the beginning of the statement:

```html
<livewire:todo-count :todos="$todos" label="Todo Count:" />
```

Boolean values may be provided to components by only specifying the key. For example, to pass an `$inline` variable with a value of `true` to a component, we may simply place `inline` on the component tag:

```html
<livewire:todo-count :todos="$todos" inline />
```

## Shortened Attribute Syntax

When passing PHP variables into a component, the variable name and the prop name are often the same. To avoid writing the name twice, Livewire allows you to simply prefix the variable with a colon:

```html
<!-- Before -->
<livewire:todo-count :todos="$todos" /> 

<!-- After -->
<livewire:todo-count :$todos /> 
```

## Rendering Children in a Loop

When rendering a child component within a loop, you should include a unique key value for each iteration.

Component keys are how Livewire tracks each component on subsequent renders, particularly if a component has already been rendered or if multiple components have been re-arranged on the page.

You can specify the component's key by specifying a `:key` prop on the child component:

```html
<div>
    <h1>Todos</h1>

    @foreach ($todos as $todo)
        <livewire:todo-item :$todo :wire:key="$todo->id" />
    @endforeach
</div>
```

As you can see, each child component will have a unique key set to the ID of each `$todo`. This ensures the key will be unique and tracked if the todos are re-ordered.

**Important:** Keys aren't optional in Livewire. If you have used frontend frameworks like Vue or Alpine, you are familiar with adding a key to a nested element in a loop. However, in those frameworks, a key isn't mandatory. In Livewire, keys are relied upon more heavily and will behave incorrectly without them.

## Reactive Props

Developers new to Livewire expect that props are "reactive" by default. In other words, they expect that when a parent changes the value of a prop being passed into a child component, the child component will automatically be updated. However, by default, Livewire props are not reactive.

When using Livewire, every component is independent. This means that when an update is triggered on the parent and a network request is dispatched, only the parent component's state is sent to the server to re-render - not the child component's. The intention behind this behavior is to only send the minimal amount of data back and forth between the server and client, making updates as performant as possible.

But, if you want or need a prop to be reactive, you can easily enable this behavior using the `#[Reactive]` attribute parameter.

### Example: Reactive Props

For example, below is the template of a parent todos component. Inside, it is rendering a `todo-count` component and passing in the current list of todos:

```html
<div>
    <h1>Todos:</h1>

    <livewire:todo-count :$todos />

    <!-- ... -->
</div>
```

Now let's add `#[Reactive]` to the `$todos` prop in the `todo-count` component. Once we have done so, any todos that are added or removed inside the parent component will automatically trigger an update within the `todo-count` component:

```php
<?php // resources/views/components/⚡todo-count.blade.php

use Livewire\Attributes\Reactive;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\Todo;

new class extends Component {
    #[Reactive] 
    public $todos;

    #[Computed]
    public function count()
    {
        return $this->todos->count(),
    }
};
?>

<div>
    Count: {{ $this->count }}
</div>
```

Reactive properties are an incredibly powerful feature, making Livewire more similar to frontend component libraries like Vue and React. But, it is important to understand the performance implications of this feature and only add `#[Reactive]` when it makes sense for your particular scenario.

## Binding to Child Data Using wire:model

Another powerful pattern for sharing state between parent and child components is using `wire:model` directly on a child component via Livewire's Modelable feature.

This behavior is very commonly needed when extracting an input element into a dedicated Livewire component while still accessing its state in the parent component.

### Example: Modelable Child Component

Below is an example of a parent todos component that contains a `$todo` property which tracks the current todo about to be added by a user:

```php
<?php // resources/views/components/⚡todos.blade.php

use Livewire\Component;

new class extends Component {
    public $todo = '';

    public function add()
    {
        // ...
    }
};
?>

<div>
    <livewire:todo-input wire:model="todo" />

    <button wire:click="add">Add</button>
</div>
```

Now, in the child `todo-input` component, you can use the `x-modelable` directive to make the component's data available for binding:

```html
<!-- resources/views/components/todo-input.blade.php -->

<input type="text" x-data="{ value: '' }" x-modelable="value" wire:model="value">
```

When the parent uses `wire:model="todo"` on the child component, Livewire will automatically bind the parent's `$todo` property to the child's `value` data.

## Communicating Back to the Parent via Events

Now we'll discuss a simple example of using an event to trigger an update in a parent component.

Consider a todos component with functionality to show and remove todos:

```php
<?php // resources/views/components/⚡todos.blade.php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\Todo;

new class extends Component {
    public function remove($todoId)
    {
        $todo = Todo::find($todoId);

        $this->authorize('delete', $todo);

        $todo->delete();
    }

    #[Computed]
    public function todos()
    {
        return Auth::user()->todos,
    }
};
?>

<div>
    @foreach ($this->todos as $todo)
        <livewire:todo-item :$todo :wire:key="$todo->id" />
    @endforeach
</div>
```

To call `remove()` from inside the child `todo-item` components, you can add an event listener to todos via the `#[On]` attribute:

```php
<?php // resources/views/components/⚡todos.blade.php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\Todo;

new class extends Component {
    #[On('remove-todo')] 
    public function remove($todoId)
    {
        $todo = Todo::find($todoId);

        $this->authorize('delete', $todo);

        $todo->delete();
    }

    #[Computed]
    public function todos()
    {
        return Auth::user()->todos,
    }
};
?>

<div>
    @foreach ($this->todos as $todo)
        <livewire:todo-item :$todo :wire:key="$todo->id" />
    @endforeach
</div>
```

Once the attribute has been added to the action, you can dispatch the `remove-todo` event from the `todo-item` child component:

```php
<?php // resources/views/components/⚡todo-item.blade.php

use Livewire\Component;
use App\Models\Todo;

new class extends Component {
    public Todo $todo;

    public function remove()
    {
        $this->dispatch('remove-todo', todoId: $this->todo->id); 
    }
};
?>

<div>
    <span>{{ $todo->content }}</span>

    <button wire:click="remove">Remove</button>
</div>
```

Now when the "Remove" button is clicked inside a `todo-item`, the parent todos component will intercept the dispatched event and perform the todo removal.

After the todo is removed in the parent, the list will be re-rendered and the child that dispatched the `remove-todo` event will be removed from the page.

### Improving Performance by Dispatching Client-Side

Though the above example works, it takes two network requests to perform a single action:

1. The first network request from the `todo-item` component triggers the `remove` action, dispatching the `remove-todo` event.
2. The second network request is after the `remove-todo` event is dispatched client-side and is intercepted by `todos` to call its `remove` action.

You can avoid the first request entirely by dispatching the `remove-todo` event directly on the client-side. Below is an updated `todo-item` component that does not trigger a network request when dispatching the `remove-todo` event:

```php
<?php // resources/views/components/⚡todo-item.blade.php

use Livewire\Component;
use App\Models\Todo;

new class extends Component {
    public Todo $todo;
};
?>

<div>
    <span>{{ $todo->content }}</span>

    <button wire:click="$dispatch('remove-todo', { todoId: {{ $todo->id }} })">Remove</button>
</div>
```

As a rule of thumb, always prefer dispatching client-side when possible.

## Directly Accessing the Parent from the Child

Event communication adds a layer of indirection. A parent can listen for an event that never gets dispatched from a child, and a child can dispatch an event that is never intercepted by a parent.

This indirection is sometimes desirable; however, in other cases you may prefer to access a parent component directly from the child component.

Livewire allows you to accomplish this by providing a magic `$parent` variable to your Blade template that you can use to access actions and properties directly from the child. Here's the above TodoItem template rewritten to call the `remove()` action directly on the parent via the magic `$parent` variable:

```html
<div>
    <span>{{ $todo->content }}</span>

    <button wire:click="$parent.remove({{ $todo->id }})">Remove</button>
</div>
```

Events and direct parent communication are a few of the ways to communicate back and forth between parent and child components. Understanding their tradeoffs enables you to make more informed decisions about which pattern to use in a particular scenario.

## Dynamic Child Components

Sometimes, you may not know which child component should be rendered on a page until run-time. Therefore, Livewire allows you to choose a child component at run-time via `<livewire:dynamic-component ...>`, which receives an `:is` prop:

```html
<livewire:dynamic-component :is="$current" />
```

Dynamic child components are useful in a variety of different scenarios, but below is an example of rendering different steps in a multi-step form using a dynamic component:

```php
<?php // resources/views/components/⚡steps.blade.php

use Livewire\Component;

new class extends Component {
    public $current = 'step-one';

    protected $steps = [
        'step-one',
        'step-two',
        'step-three',
    ];

    public function next()
    {
        $currentIndex = array_search($this->current, $this->steps);

        $this->current = $this->steps[$currentIndex + 1];
    }
};
?>

<div>
    <livewire:dynamic-component :is="$current" :wire:key="$current" />

    <button wire:click="next">Next</button>
</div>
```

Now, if the steps component's `$current` prop is set to "step-one", Livewire will render a component named "step-one" like so:

```php
<?php // resources/views/components/⚡step-one.blade.php

use Livewire\Component;

new class extends Component {
    //
};
?>

<div>
    Step One Content
</div>
```

If you prefer, you can use the alternative syntax:

```html
<livewire:is :component="$current" :wire:key="$current" />
```

**Important:** Don't forget to assign each child component a unique key. Although Livewire automatically generates a key for `<livewire:dynamic-child />` and `<livewire:is />`, that same key will apply to all your child components, meaning subsequent renders will be skipped.

## Recursive Components

Although rarely needed by most applications, Livewire components may be nested recursively, meaning a parent component renders itself as its child.

Imagine a survey which contains a `survey-question` component that can have sub-questions attached to itself:

```php
<?php // resources/views/components/⚡survey-question.blade.php

use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\Question;

new class extends Component {
    public Question $question;

    #[Computed]
    public function subQuestions()
    {
        return $this->question->subQuestions,
    }
};
?>

<div>
    Question: {{ $question->content }}

    @foreach ($this->subQuestions as $subQuestion)
        <livewire:survey-question :question="$subQuestion" :wire:key="$subQuestion->id" />
    @endforeach
</div>
```

Of course, the standard rules of recursion apply to recursive components. Most importantly, you should have logic in your template to ensure the template doesn't recurse indefinitely. In the example above, if a `$subQuestion` contained the original question as its own `$subQuestion`, an infinite loop would occur.

## Forcing a Child Component to Re-Render

Behind the scenes, Livewire generates a key for each nested Livewire component in its template.

For example, consider the following nested `todo-count` component:

```html
<div>
    <livewire:todo-count :$todos />
</div>
```

Livewire internally attaches a random string key to the component like so:

```html
<div>
    <livewire:todo-count :$todos wire:key="lska" />
</div>
```

When the parent component is rendering and encounters a child component like the above, it stores the key in a list of children attached to the parent.

Livewire uses this list for reference on subsequent renders in order to detect if a child component has already been rendered in a previous request. If it has already been rendered, the component is skipped. If the child key is not in the list, meaning it hasn't been rendered already, Livewire will create a new instance of the component and render it in place.

Using this knowledge, if you want to force a component to re-render, you can simply change its key.

Below is an example where we might want to destroy and re-initialize the `todo-count` component if the `$todos` being passed to the component are changed:

```html
<div>
    <livewire:todo-count :todos="$todos" :wire:key="$todos->pluck('id')->join('-')" />
</div>
```

As you can see above, we are generating a dynamic `:key` string based on the content of `$todos`. This way, the `todo-count` component will render and exist as normal until the `$todos` themselves change. At that point, the component will be re-initialized entirely from scratch, and the old component will be discarded.

## See Also

- [Events](./events.md) — Communicate between nested components
- [Components](./components.md) — Learn about rendering and organizing components
- [Islands](../features/islands.md) — Alternative to nesting for isolated updates
- [Reactive Attribute](../php-attributes/reactive.md) — Make props reactive in nested components
