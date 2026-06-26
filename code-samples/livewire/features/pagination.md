# Pagination

**Source URL:** https://livewire.laravel.com/docs/4.x/pagination

## Overview

Laravel's pagination feature allows you to query a subset of data and provides your users with the ability to navigate between pages of those results.

Because Laravel's paginator was designed for static applications, in a non-Livewire app, each page navigation triggers a full browser visit to a new URL containing the desired page (`?page=2`).

However, when you use pagination inside a Livewire component, users can navigate between pages while remaining on the same page. Livewire will handle everything behind the scenes, including updating the URL query string with the current page.

## Basic Usage

Below is the most basic example of using pagination inside a `show-posts` component to only show ten posts at a time:

**Important:** You must use the `WithPagination` trait to take advantage of Livewire's pagination features. Each component containing pagination must use the `Livewire\WithPagination` trait.

```php
<?php // resources/views/components/⚡show-posts.blade.php

use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Livewire\Component;
use App\Models\Post;

new class extends Component {
    use WithPagination;

    #[Computed]
    public function posts()
    {
        return Post::paginate(10);
    }
};
?>

<div>
    <div>
        @foreach ($this->posts as $post)
            <!-- ... -->
        @endforeach
    </div>

    {{ $this->posts->links() }}
</div>
```

As you can see, in addition to limiting the number of posts shown via the `Post::paginate()` method, we will also use `$this->posts->links()` to render page navigation links.

## Disabling URL Query String Tracking

By default, Livewire's paginator tracks the current page in the browser URL's query string like so: `?page=2`.

If you wish to still use Livewire's pagination utility, but disable query string tracking, you can do so using the `WithoutUrlPagination` trait:

```php
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Livewire\Component;

class ShowPosts extends Component
{
    use WithPagination, WithoutUrlPagination; 

    // ...
}
```

Now, pagination will work as expected, but the current page won't show up in the query string. This also means the current page won't be persisted across page changes.

## Customizing Scroll Behavior

By default, Livewire's paginator scrolls to the top of the page after every page change.

You can disable this behavior by passing `false` to the `scrollTo` parameter of the `links()` method like so:

```html
{{ $posts->links(data: ['scrollTo' => false]) }}
```

Alternatively, you can provide any CSS selector to the `scrollTo` parameter, and Livewire will find the nearest element matching that selector and scroll to it after each navigation:

```html
{{ $posts->links(data: ['scrollTo' => '#paginated-posts']) }}
```

## Resetting the Page

When sorting or filtering results, it is common to want to reset the page number back to 1.

For this reason, Livewire provides the `$this->resetPage()` method, allowing you to reset the page number from anywhere in your component.

The following component demonstrates using this method to reset the page after the search form is submitted:

```php
<?php // resources/views/components/⚡search-posts.blade.php

use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Livewire\Component;
use App\Models\Post;

new class extends Component {
    use WithPagination;

    public $query = '';

    public function search()
    {
        $this->resetPage();
    }

    #[Computed]
    public function posts()
    {
        return Post::where('title', 'like', '%'.$this->query.'%')->paginate(10);
    }
};
?>

<div>
    <form wire:submit="search">
        <input type="text" wire:model="query">

        <button type="submit">Search posts</button>
    </form>

    <div>
        @foreach ($this->posts as $post)
            <!-- ... -->
        @endforeach
    </div>

    {{ $this->posts->links() }}
</div>
```

Now, if a user was on page 5 of the results and then filtered the results further by pressing "Search posts", the page would be reset back to 1.

## Available Page Navigation Methods

In addition to `$this->resetPage()`, Livewire provides other useful methods for navigating between pages programmatically from your component:

| Method | Description |
| --- | --- |
| `$this->setPage($page)` | Set the paginator to a specific page number |
| `$this->resetPage()` | Reset the page back to 1 |
| `$this->nextPage()` | Go to the next page |
| `$this->previousPage()` | Go to the previous page |

## Multiple Paginators

Because both Laravel and Livewire use URL query string parameters to store and track the current page number, if a single page contains multiple paginators, it's important to assign them different names.

To demonstrate the problem more clearly, consider the following `show-clients` component:

```php
<?php // resources/views/components/⚡show-clients.blade.php

use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Livewire\Component;
use App\Models\Client;

new class extends Component {
    use WithPagination;

    #[Computed]
    public function clients()
    {
        return Client::paginate(10);
    }

    #[Computed]
    public function invoices()
    {
        return Invoice::paginate(10);
    }
};
?>

<div>
    <h2>Clients</h2>
    @foreach ($this->clients as $client)
        <!-- ... -->
    @endforeach
    {{ $this->clients->links() }}

    <h2>Invoices</h2>
    @foreach ($this->invoices as $invoice)
        <!-- ... -->
    @endforeach
    {{ $this->invoices->links() }}
</div>
```

In the above example, both paginators will use the same `?page=` query string parameter, causing conflicts when navigating between pages.

To fix this, you can pass a `pageName` parameter to the `paginate()` method:

```php
#[Computed]
public function clients()
{
    return Client::paginate(10, pageName: 'clientsPage');
}

#[Computed]
public function invoices()
{
    return Invoice::paginate(10, pageName: 'invoicesPage');
}
```

Now, the clients paginator will use `?clientsPage=2` and the invoices paginator will use `?invoicesPage=2`, allowing both to work independently.

## See Also

- [Properties](../essentials/properties.md) — Manage component state
- [Computed Properties](./computed-properties.md) — Create derived values
- [URL Query Parameters](./url.md) — Track state in the URL
