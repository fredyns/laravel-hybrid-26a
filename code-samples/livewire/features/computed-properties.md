# Computed Properties

**Source URL:** https://livewire.laravel.com/docs/4.x/computed-properties

## Overview

Computed properties are a way to create "derived" properties in Livewire. Like accessors on an Eloquent model, computed properties allow you to access values and memoize them for future access during the request.

Computed properties are particularly useful in combination with component's public properties.

## Basic Usage

To create a computed property, you can add the `#[Computed]` attribute above any method in your Livewire component. Once the attribute has been added to the method, you can access it like any other property.

**Important:** Make sure you import attribute classes. For example, the `#[Computed]` attribute requires the following import: `use Livewire\Attributes\Computed;`

For example, here's a `show-user` component that uses a computed property named `user()` to access a User Eloquent model based on a property named `$userId`:

```php
<?php // resources/views/components/⚡show-user.blade.php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\User;

new class extends Component {
    public $userId;

    #[Computed]
    public function user()
    {
        return User::find($this->userId);
    }

    public function follow()
    {
        Auth::user()->follow($this->user);
    }
};
?>

<div>
    <h1>{{ $this->user->name }}</h1>

    <span>{{ $this->user->email }}</span>

    <button wire:click="follow">Follow</button>
</div>
```

Because the `#[Computed]` attribute has been added to the `user()` method, the value is accessible in other methods in the component and within the Blade template.

**Important:** Unlike normal properties, computed properties aren't directly available inside your component's template. Instead, you must access them on the `$this` object. For example, a computed property named `posts()` must be accessed via `$this->posts` inside your template.

**Note:** Computed properties are not supported on `Livewire\Form` objects. Trying to use a Computed property within a Form will result in an error when you attempt to access the property in blade using `$form->property` syntax.

## Performance Advantage

You may be asking yourself: why use computed properties at all? Why not just call the method directly?

Accessing a method as a computed property offers a performance advantage over calling a method. Internally, when a computed property is executed for the first time, Livewire memoizes the returned value. This way, any subsequent accesses in the request will return the memoized value instead of executing multiple times.

This allows you to freely access a derived value and not worry about the performance implications.

**Important:** Computed properties are only memoized for a single request. It's a common misconception that Livewire memoizes computed properties for the entire lifespan of your Livewire component on a page. However, this isn't the case. Instead, Livewire only memoizes the result for the duration of a single component request (it does not persist between requests). This means that if your computed property method contains an expensive database query, it will be executed every time your Livewire component performs an update.

## Clearing the Memo

Consider the following problematic scenario:

1. You access a computed property that depends on a certain property or database state
2. The underlying property or database state changes
3. The memoized value for the property becomes stale and needs to be re-computed

To clear, or "bust", the stored memo, you can use PHP's `unset()` function.

Below is an example of an action called `createPost()` that, by creating a new post in the application, makes the `posts()` computed stale — meaning the computed property `posts()` needs to be re-computed to include the newly added post:

```php
<?php // resources/views/components/⚡show-posts.blade.php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    #[Computed]
    public function posts()
    {
        return Auth::user()->posts;
    }

    public function createPost()
    {
        Auth::user()->posts()->create([
            'title' => 'New post',
        ]);

        unset($this->posts); 
    }
};
?>

<div>
    @foreach ($this->posts as $post)
        <h2>{{ $post->title }}</h2>
    @endforeach

    <button wire:click="createPost">Create post</button>
</div>
```

In the above example, when `createPost()` is called, a new post is created and then `unset($this->posts)` clears the memoized value. The next time `$this->posts` is accessed, the computed property will be re-executed and will include the newly created post.

## Caching Computed Properties

By default, computed properties are only memoized for the duration of a single request. However, you can cache the result for longer periods using Laravel's cache:

```php
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;

#[Computed]
public function expensiveData()
{
    return Cache::remember('expensive-data-'.$this->userId, 3600, function () {
        return User::find($this->userId)->expensiveOperation();
    });
}
```

## See Also

- [Properties](../essentials/properties.md) — Manage component state
- [Pagination](./pagination.md) — Paginate large datasets
- [Validation](./validation.md) — Validate component data
