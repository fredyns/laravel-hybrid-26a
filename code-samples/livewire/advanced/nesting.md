# Nesting

**Source URL:** https://livewire.laravel.com/docs/4.x/understanding-nesting

## Overview

Livewire components are nestable—meaning one component can render multiple components within itself. However, because Livewire's nesting system is built differently than other frameworks, there are important implications and constraints to be aware of.

## Every Component is Independent

In Livewire, every component on a page tracks its state and makes updates independently of other components.

Consider a Posts component with nested ShowPost components:

```php
<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Posts extends Component
{
    public $postLimit = 2;

    public function render()
    {
        return view('livewire.posts', [
            'posts' => Auth::user()->posts()
                ->limit($this->postLimit)->get(),
        ]);
    }
}
```

```html
<div>
    Post Limit: <input type="number" wire:model.live="postLimit">

    @foreach ($posts as $post)
        <livewire:show-post :$post :wire:key="$post->id">
    @endforeach
</div>
```

```php
<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Post;

class ShowPost extends Component
{
    public Post $post;

    public function render()
    {
        return view('livewire.show-post');
    }
}
```

```html
<div>
    <h1>{{ $post->title }}</h1>

    <p>{{ $post->content }}</p>

    <button wire:click="$refresh">Refresh post</button>
</div>
```

The HTML for the entire component tree on initial page load:

```html
<div wire:id="123" wire:snapshot="...">
    Post Limit: <input type="number" wire:model.live="postLimit">

    <div wire:id="456" wire:snapshot="...">
        <h1>The first post</h1>

        <p>Post content</p>

        <button wire:click="$refresh">Refresh post</button>
    </div>

    <div wire:id="789" wire:snapshot="...">
        <h1>The second post</h1>

        <p>Post content</p>

        <button wire:click="$refresh">Refresh post</button>
    </div>
</div>
```

Each component has its own ID and snapshot (`wire:id` and `wire:snapshot`) embedded in the HTML for Livewire's JavaScript core to extract and track.

## Updating a Child

When you click the "Refresh post" button in a child component, only that component's data is sent to the server:

```json
{
    "memo": { "name": "show-post", "id": "456" },
    "state": { ... }
}
```

Only that component is re-rendered and returned.

## Updating the Parent

When a user changes the "Post Limit" value from 2 to 1, an update is triggered on the parent:

```json
{
    "updates": { "postLimit": 1 },
    "snapshot": {
        "memo": { "name": "posts", "id": "123" },
        "state": { "postLimit": 2, ... }
    }
}
```

When Livewire renders the Posts component, it renders placeholders for any child components it encounters:

```html
<div wire:id="123">
    Post Limit: <input type="number" wire:model.live="postLimit">

    <div wire:id="456"></div>
</div>
```

When this HTML is received on the frontend, Livewire intelligently skips child component placeholders during morphing. The final DOM content preserves the existing child components:

```html
<div wire:id="123">
    Post Limit: <input type="number" wire:model.live="postLimit">

    <div wire:id="456">
        <h1>The first post</h1>

        <p>Post content</p>

        <button wire:click="$refresh">Refresh post</button>
    </div>
</div>
```

## Performance Implications

**Advantages:**
- Isolate expensive portions of your application
- Slow database queries in one component won't impact the rest of the page

**Drawbacks:**
- Inter-component communication becomes more difficult
- Properties passed down from parent to child aren't "reactive"
- Parent updates don't automatically update child properties

Livewire provides APIs to overcome these challenges:
- Reactive properties (`#[Reactive]`)
- Modelable components (`#[Modelable]`)
- The `$parent` object

## See Also

- [Hydration](./hydration.md) — Understand Livewire's request lifecycle
- [Nesting Components](../essentials/nesting.md) — Parent-child communication
- [Reactive](../php-attributes/reactive.md) — Make properties reactive
