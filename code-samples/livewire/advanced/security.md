# Security

**Source URL:** https://livewire.laravel.com/docs/4.x/security

## Overview

It's important to ensure your Livewire apps are secure and don't expose application vulnerabilities. Livewire has internal security features, but your application code must also keep components secure.

## Authorizing Action Parameters

Livewire actions are powerful, but any parameters passed to them are mutable on the client and should be treated as untrusted user input.

The most common security pitfall is failing to validate and authorize Livewire action calls before persisting changes:

```php
<?php

use App\Models\Post;
use Livewire\Component;

class ShowPost extends Component
{
    // ...

    public function delete($id)
    {
        // INSECURE!

        $post = Post::find($id);

        $post->delete();
    }
}
```

```html
<button wire:click="delete({{ $post->id }})">Delete Post</button>
```

The `wire:click="delete(...)"` can be modified in the browser to pass ANY post ID. Action parameters should be treated like untrusted input from the browser.

### Creating a Policy

Create a Laravel Policy for authorization:

```bash
php artisan make:policy PostPolicy --model=Post
```

Update `app/Policies/PostPolicy.php`:

```php
<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function delete(?User $user, Post $post): bool
    {
        return $user?->id === $post->user_id;
    }
}
```

Use `$this->authorize()` in the component:

```php
public function delete($id)
{
    $post = Post::find($id);

    $this->authorize('delete', $post);

    $post->delete();
}
```

## Authorizing Public Properties

Public properties should also be treated as untrusted input. A malicious user could inject:

```html
<input type="text" wire:model="postId">
```

To protect against this, use one of three solutions:

### Using Model Properties

Store the entire model instead of just the ID:

```php
<?php

use App\Models\Post;
use Livewire\Component;

class ShowPost extends Component
{
    public Post $post;

    public function mount($postId)
    {
        $this->post = Post::find($postId);
    }

    public function delete()
    {
        $this->post->delete();
    }
}
```

Livewire ensures the ID is never tampered with.

### Locking the Property

Use the `#[Locked]` attribute to prevent properties from being modified:

```php
<?php

use App\Models\Post;
use Livewire\Component;
use Livewire\Attributes\Locked;

class ShowPost extends Component
{
    #[Locked]
    public $postId;

    public function mount($postId)
    {
        $this->postId = $postId;
    }

    public function delete()
    {
        $post = Post::find($this->postId);

        $post->delete();
    }
}
```

### Authorizing the Property

Manually authorize in the action:

```php
public function delete()
{
    $post = Post::find($this->postId);

    $this->authorize('delete', $post);

    $post->delete();
}
```

## Middleware

When a Livewire component is loaded on a page with route-level Authorization Middleware, Livewire re-applies those middlewares to subsequent requests:

```php
Route::livewire('/post/{post}', App\Livewire\UpdatePost::class)
    ->middleware('can:update,post');
```

This protects against scenarios where authorization rules change after initial page-load.

### Configuring Persistent Middleware

By default, Livewire persists these middlewares:

- `EnsureFrontendRequestsAreStateful`
- `AuthenticateSession`
- `AuthenticateWithBasicAuth`
- `SubstituteBindings`
- `RedirectIfAuthenticated`
- `Authenticate`
- `Authorize`

To add custom middleware, register it in a Service Provider:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::addPersistentMiddleware([
            App\Http\Middleware\EnsureUserHasRole::class,
        ]);
    }
}
```

### Global Livewire Middleware

Apply middleware to every Livewire update request:

```php
Livewire::setUpdateRoute(function ($handle) {
    return Route::post('/livewire/update', $handle)
        ->middleware(App\Http\Middleware\LocalizeViewPaths::class);
});
```

## Snapshot Checksums

Between every Livewire request, a snapshot is taken and sent to the browser. Livewire generates a "checksum" of each snapshot to verify it hasn't been tampered with.

If a checksum mismatch is found, Livewire throws a `CorruptComponentPayloadException` and the request fails. This protects against malicious tampering.

## See Also

- [Locked](../php-attributes/locked.md) — Prevent property tampering
- [Hydration](./hydration.md) — Understand component snapshots
