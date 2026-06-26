# URL Query Parameters

**Source URL:** https://livewire.laravel.com/docs/4.x/url

## Overview

Livewire allows you to track component state in the URL query string. This enables users to bookmark and share URLs with specific component states, and allows the back/forward buttons to work as expected.

## Basic Usage

To track a property in the URL, use the `#[Url]` attribute:

```php
<?php

use Livewire\Attributes\Url;
use Livewire\Component;

class SearchPosts extends Component
{
    #[Url]
    public $search = '';

    #[Url]
    public $sort = 'date';
}
```

Now, when the `$search` or `$sort` properties change, they'll be reflected in the URL query string:

```
/posts?search=laravel&sort=title
```

## Customizing Query Parameter Names

You can customize the query parameter name using the `as` parameter:

```php
#[Url(as: 'q')]
public $search = '';
```

Now the URL will use `?q=laravel` instead of `?search=laravel`.

## Keeping State in Sync

When a user navigates to a URL with query parameters, Livewire automatically syncs those values to your component properties.

## Resetting URL State

To reset URL parameters back to their default values, use `$this->resetPage()` or manually set the properties:

```php
public function reset()
{
    $this->search = '';
    $this->sort = 'date';
}
```

## See Also

- [Properties](../essentials/properties.md) — Manage component state
- [Pagination](./pagination.md) — Paginate large datasets
- [Navigate](./navigate.md) — Build SPA-like navigation
