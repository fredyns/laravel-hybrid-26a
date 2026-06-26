# Properties

**Source URL:** https://livewire.laravel.com/docs/4.x/properties

## Overview

Properties store and manage state inside your Livewire components. They are defined as public properties on component classes and can be accessed and modified on both the server and client-side.

## Initializing Properties

You can set initial values for properties within your component's `mount()` method.

### Example: Initializing Properties

```php
<?php // resources/views/components/⚡todos.blade.php

use Livewire\Component;

new class extends Component {
    public $todos = [];

    public $todo = '';

    public function mount()
    {
        $this->todos = ['Buy groceries', 'Walk the dog', 'Write code']; 
    }

    // ...
};
```

In this example, we've defined an empty `todos` array and initialized it with a default list of todos in the `mount()` method. Now, when the component renders for the first time, these initial todos are shown to the user.

## Bulk Assignment

Sometimes initializing many properties in the `mount()` method can feel verbose. To help with this, Livewire provides a convenient way to assign multiple properties at once via the `fill()` method. By passing an associative array of property names and their respective values, you can set several properties simultaneously and cut down on repetitive lines of code in `mount()`.

### Using fill() for Bulk Assignment

```php
<?php // resources/views/components/post/⚡edit.blade.php

use Livewire\Component;
use App\Models\Post;

new class extends Component {
    public $post;

    public $title;

    public $description;

    public function mount(Post $post)
    {
        $this->post = $post;

        $this->fill( 
            $post->only('title', 'description'), 
        ); 
    }

    // ...
};
```

Because `$post->only(...)` returns an associative array of model attributes and values based on the names you pass into it, the `$title` and `$description` properties will be initially set to the title and description of the `$post` model from the database without having to set each one individually.

## Data Binding

Livewire supports two-way data binding through the `wire:model` HTML attribute. This allows you to easily synchronize data between component properties and HTML inputs, keeping your user interface and component state in sync.

### Example: Two-Way Data Binding

```php
<?php // resources/views/components/⚡todos.blade.php

use Livewire\Component;

new class extends Component {
    public $todos = [];

    public $todo = '';

    public function add()
    {
        $this->todos[] = $this->todo;

        $this->todo = '';
    }

    // ...
};
```

```html
<div>
    <input type="text" wire:model="todo" placeholder="Todo..."> 

    <button wire:click="add">Add Todo</button>

    <ul>
        @foreach ($todos as $todo)
            <li wire:key="{{ $loop->index }}">{{ $todo }}</li>
        @endforeach
    </ul>
</div>
```

In the above example, the text input's value will synchronize with the `$todo` property on the server when the "Add Todo" button is clicked.

This is just scratching the surface of `wire:model`. For deeper information on data binding, check out the [Forms](./forms.md) documentation.

## Resetting Properties

Sometimes, you may need to reset your properties back to their initial state after an action is performed by the user. In these cases, Livewire provides a `reset()` method that accepts one or more property names and resets their values to their initial state.

### Using reset() to Clear Properties

```php
<?php // resources/views/components/⚡todos.blade.php

use Livewire\Component;

new class extends Component {
    public $todos = [];

    public $todo = '';

    public function addTodo()
    {
        $this->todos[] = $this->todo;

        $this->reset('todo'); 
    }

    // ...
};
```

In the above example, after a user clicks "Add Todo", the input field holding the todo that has just been added will clear, allowing the user to write a new todo.

**Important:** `reset()` will reset a property to its state before the `mount()` method was called. If you initialized the property in `mount()` to a different value, you will need to reset the property manually.

## Pulling Properties

Alternatively, you can use the `pull()` method to both reset and retrieve the value in one operation.

### Using pull() to Reset and Retrieve

```php
<?php // resources/views/components/⚡todos.blade.php

use Livewire\Component;

new class extends Component {
    public $todos = [];

    public $todo = '';

    public function addTodo()
    {
        $this->todos[] = $this->pull('todo'); 
    }

    // ...
};
```

The above example is pulling a single value, but `pull()` can also be used to reset and retrieve (as a key-value pair) all or some properties:

```php
// The same as $this->all() and $this->reset();
$this->pull();

// The same as $this->only(...) and $this->reset(...);
$this->pull(['title', 'content']);
```

## Supported Property Types

Livewire supports a limited set of property types because of its unique approach to managing component data between server requests.

Each property in a Livewire component is serialized or "dehydrated" into JSON between requests, then "hydrated" from JSON back into PHP for the next request.

This two-way conversion process has certain limitations, restricting the types of properties Livewire can work with.

### Primitive Types

Livewire supports primitive types such as strings, integers, etc. These types can be easily converted to and from JSON, making them ideal for use as properties in Livewire components.

**Supported primitive property types:** Array, String, Integer, Float, Boolean, and Null.

```php
new class extends Component {
    public array $todos = [];

    public string $todo = '';

    public int $maxTodos = 10;

    public bool $showTodos = false;

    public ?string $todoFilter = null;
};
```

### Common PHP Types

In addition to primitive types, Livewire supports common PHP object types used in Laravel applications. However, it's important to note that these types will be dehydrated into JSON and hydrated back to PHP on each request. This means that the property may not preserve run-time values such as closures. Also, information about the object such as class names may be exposed to JavaScript.

**Supported PHP types:**

| Type | Full Class Name |
| --- | --- |
| BackedEnum | `BackedEnum` |
| Collection | `Illuminate\Support\Collection` |
| Eloquent Collection | `Illuminate\Database\Eloquent\Collection` |
| Model | `Illuminate\Database\Eloquent\Model` |
| DateTime | `DateTime` |
| Carbon | `Carbon\Carbon` |
| Stringable | `Illuminate\Support\Stringable` |

#### Eloquent Collections and Models

When storing Eloquent Collections and Models in Livewire properties, be aware of these limitations:

- **Query constraints aren't preserved:** Additional query constraints like `select(...)` will not be re-applied on subsequent requests.
- **Performance impact:** Storing large Eloquent collections as properties can cause performance issues because Livewire must re-execute the database query every time the component hydrates. For expensive queries, consider using computed properties instead, which only execute when the data is actually accessed in your template.

### Example: Setting Properties as Various Types

```php
public function mount()
{
    $this->todos = collect([]); // Collection

    $this->todos = Todos::all(); // Eloquent Collection

    $this->todo = Todos::first(); // Model

    $this->date = new DateTime('now'); // DateTime

    $this->date = new Carbon('now'); // Carbon

    $this->todo = str(''); // Stringable
}
```

## Supporting Custom Types

Livewire allows your application to support custom types through two powerful mechanisms:

- **Wireables** - Simple and easy to use for most applications
- **Synthesizers** - For advanced users and package authors wanting more flexibility

### Wireables

Wireables are any class in your application that implements the `Wireable` interface.

For example, let's imagine you have a `Customer` object in your application that contains the primary data about a customer:

```php
class Customer
{
    protected $name;
    protected $age;

    public function __construct($name, $age)
    {
        $this->name = $name;
        $this->age = $age;
    }
}
```

To make this class Wireable, implement the `Wireable` interface:

```php
use Livewire\Wireable;

class Customer implements Wireable
{
    protected $name;
    protected $age;

    public function __construct($name, $age)
    {
        $this->name = $name;
        $this->age = $age;
    }

    public function toLivewire()
    {
        return [
            'name' => $this->name,
            'age' => $this->age,
        ];
    }

    public static function fromLivewire($value)
    {
        return new static(
            $value['name'],
            $value['age'],
        );
    }
}
```

Now you can use `Customer` as a property type in your Livewire components:

```php
new class extends Component {
    public Customer $customer;

    public function mount()
    {
        $this->customer = new Customer('John', 30);
    }
};
```

## Security Considerations

### Don't Trust Property Values

Public properties in Livewire components can be manipulated on the client-side, just like form inputs in traditional web applications. Always treat public properties as user input — as if they were request input from a traditional endpoint. In light of this, it's essential to validate and authorize properties before persisting them to a database — just like you would do when working with request input in a controller.

### Vulnerable Example

To demonstrate how neglecting to authorize and validate properties can introduce security holes in your application, the following `post.edit` component is vulnerable to attack:

```php
<?php // resources/views/components/post/⚡edit.blade.php

use Livewire\Component;
use App\Models\Post;

new class extends Component {
    public $id;
    public $title;
    public $content;

    public function mount(Post $post)
    {
        $this->id = $post->id;
        $this->title = $post->title;
        $this->content = $post->content;
    }

    public function update()
    {
        $post = Post::findOrFail($this->id);

        $post->update([
            'title' => $this->title,
            'content' => $this->content,
        ]);

        session()->flash('message', 'Post updated successfully!');
    }
};
```

Because we are storing the `id` of the post as a public property on the component, it can be manipulated on the client just the same as the `title` and `content` properties. A malicious user can easily change the view using their browser DevTools and update any post they want.

### Authorizing the Input

Because `$id` can be manipulated client-side with something like `wire:model`, just like in a controller, we can use Laravel's authorization to make sure the current user can update the post:

```php
public function update()
{
    $post = Post::findOrFail($this->id);

    $this->authorize('update', $post); 

    $post->update(...);
}
```

If a malicious user mutates the `$id` property, the added authorization will catch it and throw an error.

### Locking the Property

Livewire also allows you to "lock" properties in order to prevent properties from being modified on the client-side. You can "lock" a property from client-side manipulation using the `#[Locked]` attribute:

```php
use Livewire\Attributes\Locked;
use Livewire\Component;

new class extends Component {
    #[Locked] 
    public $id;

    // ...
};
```

Now, if a user tries to modify `$id` on the front end, an error will be thrown. By using `#[Locked]`, you can assume this property has not been manipulated anywhere outside your component's class.

### Eloquent Models and Locking

When an Eloquent model is assigned to a Livewire component property, Livewire will automatically lock the property and ensure the ID isn't changed, so that you are safe from these kinds of attacks:

```php
<?php // resources/views/components/post/⚡edit.blade.php

use Livewire\Component;
use App\Models\Post;

new class extends Component {
    public Post $post; 
    public $title;
    public $content;

    public function mount(Post $post)
    {
        $this->post = $post;
        $this->title = $post->title;
        $this->content = $post->content;
    }

    public function update()
    {
        $this->post->update([
            'title' => $this->title,
            'content' => $this->content,
        ]);

        session()->flash('message', 'Post updated successfully!');
    }
};
```

## Properties Expose System Information to the Browser

Another essential thing to remember is that Livewire properties are serialized or "dehydrated" before they are sent to the browser. This means that their values are converted to a format that can be sent over the wire and understood by JavaScript. This format can expose information about your application to the browser, including the names and class names of your properties.

For example, suppose you have a Livewire component that defines a public property named `$post`. This property contains an instance of a `Post` model from your database. In this case, the dehydrated value of this property sent over the wire might look something like this:

```json
{
    "type": "model",
    "class": "App\\Models\\Post",
    "key": 1,
    "relationships": []
}
```

As you can see, the dehydrated value of the `$post` property includes the class name of the model (`App\Models\Post`) as well as the ID and any relationships that have been eager-loaded.

### Using morphMap to Hide Class Names

If you don't want to expose the class name of the model, you can use Laravel's "morphMap" functionality from a service provider to assign an alias to a model class name:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Relation::morphMap([
            'post' => 'App\Models\Post',
        ]);
    }
}
```

Now, when the Eloquent model is "dehydrated" (serialized), the original class name won't be exposed, only the "post" alias:

```json
{
    "type": "model",
    "class": "post", 
    "key": 1,
    "relationships": []
}
```

## Eloquent Constraints Aren't Preserved Between Requests

Typically, Livewire is able to preserve and recreate server-side properties between requests; however, there are certain scenarios where preserving values are impossible between requests.

For example, when storing Eloquent collections as Livewire properties, additional query constraints like `select(...)` will not be re-applied on subsequent requests.

### Example: Query Constraints Lost

```php
<?php // resources/views/components/⚡show-todos.blade.php

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component {
    public $todos;

    public function mount()
    {
        $this->todos = Auth::user()
            ->todos()
            ->select(['title', 'content']) 
            ->get();
    }
};
```

When this component is initially loaded, the `$todos` property will be set to an Eloquent collection of the user's todos; however, only the title and content fields of each row in the database will have been queried and loaded into each of the models.

When Livewire hydrates the JSON of this property back into PHP on a subsequent request, the `select` constraint will have been lost.

### Using Computed Properties Instead

To ensure the integrity of Eloquent queries, we recommend that you use computed properties instead of properties.

Computed properties are methods in your component marked with the `#[Computed]` attribute. They can be accessed as a dynamic property that isn't stored as part of the component's state but is instead evaluated on-the-fly.

Here's the above example re-written using a computed property:

```php
<?php // resources/views/components/⚡show-todos.blade.php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    #[Computed] 
    public function todos()
    {
        return Auth::user()
            ->todos()
            ->select(['title', 'content'])
            ->get();
    }
};
```

Here's how you would access these todos from the Blade view:

```html
<ul>
    @foreach ($this->todos as $todo)
        <li wire:key="{{ $loop->index }}">{{ $todo }}</li>
    @endforeach
</ul>
```

Notice, inside your views, you can only access computed properties on the `$this` object like so: `$this->todos`.

You can also access `$todos` from inside your class. For example, if you had a `markAllAsComplete()` action:

```php
<?php // resources/views/components/⚡show-todos.blade.php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    #[Computed]
    public function todos()
    {
        return Auth::user()
            ->todos()
            ->select(['title', 'content'])
            ->get();
    }

    public function markAllComplete() 
    {
        $this->todos->each->complete();
    }
};
```

### Why Use #[Computed]?

You might wonder why not just call `$this->todos()` as a method directly where you need to? Why use `#[Computed]` in the first place?

The reason is that computed properties have a performance advantage, since they are automatically memoized after their first usage during a single request. This means you can freely access `$this->todos` within your component and be assured that the actual method will only be called once, so that you don't run an expensive query multiple times in the same request.

## See Also

- [Forms](./forms.md) — Bind properties to form inputs with `wire:model`
- [Computed Properties](../features/computed-properties.md) — Create derived values with automatic memoization
- [Validation](../features/validation.md) — Validate property values before persisting
- [Locked Attribute](../php-attributes/locked.md) — Prevent properties from being manipulated client-side
- [Alpine](../features/alpine.md) — Access and manipulate properties from JavaScript
