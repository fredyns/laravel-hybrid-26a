# Redirecting

**Source URL:** https://livewire.laravel.com/docs/4.x/redirecting

## Overview

After a user performs some action — like submitting a form — you may want to redirect them to another page in your application.

Because Livewire requests aren't standard full-page browser requests, standard HTTP redirects won't work. Instead, you need to trigger redirects via JavaScript. Fortunately, Livewire exposes a simple `$this->redirect()` helper method to use within your components. Internally, Livewire will handle the process of redirecting on the frontend.

If you prefer, you can use Laravel's built-in redirect utilities within your components as well.

## Basic Usage

Below is an example of a `post.create` Livewire component that redirects the user to another page after they submit the form to create a post:

```php
<?php

use Livewire\Component;
use App\Models\Post;

new class extends Component {
    public $title = '';

    public $content = '';

    public function save()
    {
        Post::create([
            'title' => $this->title,
            'content' => $this->content,
        ]);

        $this->redirect('/posts'); 
    }
};
?>

<form wire:submit="save">
    <!-- Form fields... -->
</form>
```

As you can see, when the `save` action is triggered, a redirect will also be triggered to `/posts`. When Livewire receives this response, it will redirect the user to the new URL on the frontend.

## Redirect to Route

In case you want to redirect to a page using its route name you can use `redirectRoute()`.

For example, if you have a page with the route named 'profile' like this:

```php
Route::get('/user/profile', function () {
    // ...
})->name('profile');
```

You can use `redirectRoute()` to redirect to that page using the name of the route like so:

```php
$this->redirectRoute('profile');
```

In case you need to pass parameters to the route you may use the second argument of the method `redirectRoute()` like so:

```php
$this->redirectRoute('profile', ['id' => 1]);
```

## Redirect to Intended

In case you want to redirect the user back to the previous page they were on you can use `redirectIntended()`. It accepts an optional default URL as its first argument which is used as a fallback if no previous page can be determined:

```php
$this->redirectIntended('/default/url');
```

## Redirecting to Full-Page Components

Because Livewire uses Laravel's built-in redirection feature, you can use all of the redirection methods available to you in a typical Laravel application.

For example, if you are using a Livewire component as a full-page component for a route like so:

```php
Route::livewire('/posts', 'pages::show-posts');
```

You can redirect to it simply by using the route path:

```php
public function save()
{
    // ...

    $this->redirect('/posts');
}
```

## Redirect to Controller Actions

If you want to redirect to a route handled by a controller action, you can use `redirectAction()`:

```php
$this->redirectAction([UserController::class, 'index']);
```

You can pass parameters to the controller action as the second argument:

```php
$this->redirectAction([UserController::class, 'show'], ['id' => 1]);
```

## Redirect with Flash Data

You can flash data to the session before redirecting, which will be available on the next page:

```php
public function save()
{
    Post::create([
        'title' => $this->title,
        'content' => $this->content,
    ]);

    session()->flash('status', 'Post created successfully!');

    $this->redirect('/posts');
}
```

Then on the redirected page, you can access the flashed data:

```html
@if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif
```

## See Also

- [Actions](../essentials/actions.md) — Trigger component methods
- [Forms](../essentials/forms.md) — Handle form submissions
- [Pages](../essentials/pages.md) — Use components as full pages
