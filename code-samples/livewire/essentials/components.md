# Components

**Source URL:** https://livewire.laravel.com/docs/4.x/components

## Overview

Livewire components are essentially PHP classes with properties and methods that can be called directly from a Blade template. This powerful combination allows you to create full-stack interactive interfaces with a fraction of the effort and complexity of modern JavaScript alternatives.

This guide covers everything you need to know about creating, rendering, and organizing Livewire components. You'll learn about the different component formats available (single-file, multi-file, and class-based), how to pass data between components, and how to use components as full pages.

## Creating Components

You can create a component using the `make:livewire` Artisan command:

```bash
php artisan make:livewire post.create
```

This creates a single-file component at:

```
resources/views/components/post/⚡create.blade.php
```

### Single-File Component Example

```php
<?php

use Livewire\Component;

new class extends Component {
    public $title = '';

    public function save()
    {
        // Save logic here...
    }
};
?>

<div>
    <input wire:model="title" type="text">
    <button wire:click="save">Save Post</button>
</div>
```

### Why the ⚡ Emoji?

The lightning bolt in the filename makes Livewire components instantly recognizable in your editor's file tree and search results. It's a Unicode character that works seamlessly across all platforms — Windows, macOS, Linux, Git, and production servers.

The emoji is optional. To disable it, update your `config/livewire.php` file:

```php
'make_command' => [
    'emoji' => false,
],
```

## Creating Page Components

When creating components that will be used as full pages, use the `pages::` namespace:

```bash
php artisan make:livewire pages::post.create
```

This creates the component at `resources/views/pages/post/⚡create.blade.php`. This organization makes it clear which components are pages versus reusable UI components.

## Multi-File Components

As your component grows, you might find the single-file approach limiting. Livewire offers a multi-file alternative that splits your component into separate files for better organization and IDE support.

To create a multi-file component, pass the `--mfc` flag:

```bash
php artisan make:livewire post.create --mfc
```

This creates a directory with all related files together:

```
resources/views/components/post/⚡create/
├── create.php          # PHP class
├── create.blade.php    # Blade template
├── create.js           # JavaScript (optional)
├── create.css          # Scoped styles (optional)
├── create.global.css   # Global styles (optional)
└── create.test.php     # Pest test (optional, with --test flag)
```

### Multi-File Component Structure

**PHP Class File (create.php):**

```php
<?php

use Livewire\Component;

new class extends Component {
    public $title = '';

    public function save()
    {
        // Save logic here...
    }
};
```

**Blade Template (create.blade.php):**

```html
<div>
    <input wire:model="title" type="text">
    <button wire:click="save">Save Post</button>
</div>
```

## Converting Between Formats

Livewire provides the `livewire:convert` command to seamlessly convert components between single-file and multi-file formats.

### Auto-Detect and Convert

```bash
php artisan livewire:convert post.create
# Single-file → Multi-file (or vice versa)
```

### Convert to Multi-File

```bash
php artisan livewire:convert post.create --mfc
```

This will parse your single-file component, create a directory structure, split the files, and delete the original.

### Convert to Single-File

```bash
php artisan livewire:convert post.create --sfc
```

This combines all files back into a single file and deletes the directory.

**Note:** Test files are deleted when converting to single-file. If your multi-file component has a test file, you'll be prompted to confirm before conversion.

## When to Use Each Format

### Single-File Components (Default)

- Best for most components
- Keeps related code together
- Easy to understand at a glance
- Perfect for small to medium components

### Multi-File Components

- Better for large, complex components
- Improved IDE support and navigation
- Clearer separation when components have significant JavaScript

### Class-Based Components

- Familiar to developers from Livewire v2/v3
- Traditional Laravel separation of concerns
- Better for teams with established conventions

## Rendering Components

You can include a Livewire component within any Blade template using the `<livewire:component-name />` syntax:

```html
<livewire:component-name />
```

If the component is located in a sub-directory, use the dot (.) character:

```html
<!-- For: resources/views/components/post/⚡create.blade.php -->
<livewire:post.create />
```

For namespaced components—like `pages::`—use the namespace prefix:

```html
<livewire:pages::post.create />
```

## Passing Props

To pass data into a Livewire component, use prop attributes on the component tag:

```html
<livewire:post.create title="Initial Title" />
```

For dynamic values or variables, prefix the attribute with a colon:

```html
<livewire:post.create :title="$initialTitle" />
```

### Receiving Props in Component

Data passed into components is received through the `mount()` method:

```php
<?php

use Livewire\Component;

new class extends Component {
    public $title;

    public function mount($title = null)
    {
        $this->title = $title;
    }

    // ...
};
```

You can think of `mount()` as a class constructor. It runs when the component initializes, but not on subsequent requests within a page's session.

### Automatic Property Assignment

To reduce boilerplate code, you can omit the `mount()` method and Livewire will automatically set any properties with names matching the passed values:

```php
<?php

use Livewire\Component;

new class extends Component {
    public $title; // Automatically set from prop

    // ...
};
```

**Important:** These properties are not reactive by default. The `$title` property will not update automatically if the outer `:title="$initialValue"` changes after the initial page load.

## Passing Route Parameters as Props

When using components as pages, you can pass route parameters directly to your component. The route parameters are automatically passed to the `mount()` method:

```php
Route::livewire('/posts/{id}', 'pages::post.show');
```

```php
<?php // resources/views/pages/post/⚡show.blade.php

use Livewire\Component;

new class extends Component {
    public $postId;

    public function mount($id)
    {
        $this->postId = $id;
    }
};
```

### Route Model Binding

Livewire also supports Laravel's route model binding:

```php
Route::livewire('/posts/{post}', 'pages::post.show');
```

```php
<?php // resources/views/pages/post/⚡show.blade.php

use App\Models\Post;
use Livewire\Component;

new class extends Component {
    public Post $post; // Automatically bound from route

    // No mount() needed - Livewire handles it automatically
};
```

## Page Components

Components can be routed to directly as full pages using `Route::livewire()`. This is one of Livewire's most powerful features, allowing you to build entire pages without traditional controllers.

```php
Route::livewire('/posts/create', 'pages::post.create');
```

When a user visits `/posts/create`, Livewire will render the `pages::post.create` component inside your application's layout file.

Page components work just like regular components, but they're rendered as full pages with access to:

- Custom layouts
- Page titles
- Route parameters and model binding
- Named slots for layouts

## Accessing Data in Views

Livewire provides several ways to pass data to your component's Blade view. Each approach has different performance and security characteristics.

### Component Properties

The simplest approach is using public properties, which are automatically available in your Blade template:

```php
<?php

use Livewire\Component;

new class extends Component {
    public $title = 'My Post';
};
```

```html
<div>
    <h1>{{ $title }}</h1>
</div>
```

### Protected Properties

Protected properties must be accessed with `$this->`:

```php
public $title = 'My Post';           // Available as {{ $title }}
protected $apiKey = 'secret-key';    // Available as {{ $this->apiKey }}
```

**Important:** Protected properties are never sent to the frontend and cannot be manipulated by users. This makes them safe for sensitive data. However, they are not persisted between requests, which limits their usefulness in most Livewire scenarios.

### Computed Properties

Computed properties are methods that act like memoized properties. They're perfect for expensive operations like database queries:

```php
use Livewire\Attributes\Computed;

#[Computed]
public function posts()
{
    return Post::with('author')->latest()->get();
}
```

```html
<div>
    @foreach ($this->posts as $post)
        <article wire:key="{{ $post->id }}">{{ $post->title }}</article>
    @endforeach
</div>
```

Notice the `$this->` prefix - this tells Livewire to call the method and cache the result for the current request only (not between requests).

### Passing Data from render()

Similar to a controller, you can pass data directly to the view using the `render()` method:

```php
public function render()
{
    return $this->view([
        'author' => Auth::user(),
        'currentTime' => now(),
    ]);
}
```

**Note:** `render()` runs on every component update, so avoid expensive operations here unless you need fresh data on every update.

## Organizing Components

While Livewire automatically discovers components in the default `resources/views/components/` directory, you can customize where Livewire looks for components and organize them using namespaces.

### Component Namespaces

Component namespaces allow you to organize components into dedicated directories with a clean reference syntax.

By default, Livewire provides two namespaces:

- `pages::` — Points to `resources/views/pages/`
- `layouts::` — Points to `resources/views/layouts/`

You can define additional namespaces in your `config/livewire.php` file:

```php
'component_namespaces' => [
    'layouts' => resource_path('views/layouts'),
    'pages' => resource_path('views/pages'),
    'admin' => resource_path('views/admin'),    // Custom namespace
    'widgets' => resource_path('views/widgets'), // Another custom namespace
],
```

Then use them when creating, rendering, and routing:

```bash
php artisan make:livewire admin::users-table
```

```html
<livewire:admin::users-table />
```

```php
Route::livewire('/admin/users', 'admin::users-table');
```

### Additional Component Locations

If you want Livewire to discover components in additional directories beyond the defaults, configure them in your `config/livewire.php` file:

```php
'component_locations' => [
    resource_path('views/components'),
    resource_path('views/admin/components'),
    resource_path('views/widgets'),
],
```

Now Livewire will automatically discover components in all these directories.

### Programmatic Registration

For more dynamic scenarios (like package development or runtime configuration), you can register components, locations, and namespaces programmatically in a service provider:

#### Register an Individual Component

```php
use Livewire\Livewire;

// In a service provider's boot() method (e.g., App\Providers\AppServiceProvider)
Livewire::addComponent(
    name: 'custom-button',
    viewPath: resource_path('views/ui/button.blade.php')
);
```

#### Register a Component Directory

```php
Livewire::addLocation(
    viewPath: resource_path('views/admin/components')
);
```

#### Register a Namespace

```php
Livewire::addNamespace(
    namespace: 'ui',
    viewPath: resource_path('views/ui')
);
```

#### Register Class-Based Components

```php
use Livewire\Livewire;

// Register an individual class-based component
Livewire::addComponent(
    name: 'todos',
    class: \App\Livewire\Todos::class
);

// Register a location for class-based components
Livewire::addLocation(
    classNamespace: 'App\\Admin\\Livewire'
);

// Create a namespace for class-based components
Livewire::addNamespace(
    namespace: 'admin',
    classNamespace: 'App\\Admin\\Livewire',
    classPath: app_path('Admin/Livewire'),
    classViewPath: resource_path('views/admin/livewire')
);
```

## Class-Based Components

For teams migrating from Livewire v3 or those who prefer a more traditional Laravel structure, Livewire fully supports class-based components. This approach separates the PHP class and Blade view into different files in their conventional locations.

### Creating Class-Based Components

```bash
php artisan make:livewire CreatePost --class
```

This creates two separate files:

**app/Livewire/CreatePost.php:**

```php
<?php

namespace App\Livewire;

use Livewire\Component;

class CreatePost extends Component
{
    public function render()
    {
        return view('livewire.create-post');
    }
}
```

**resources/views/livewire/create-post.blade.php:**

```html
<div>
    {{-- ... --}}
</div>
```

### When to Use Class-Based Components

**Use class-based components when:**

- Migrating from Livewire v2/v3
- Your team prefers a more traditional file structure
- You have established conventions around class-based architecture

**Use single-file or multi-file components when:**

- Starting a new Livewire v4 project
- You want better component colocation
- You want to use the latest Livewire conventions

### Configuring Default Component Type

If you want class-based components by default, configure it in `config/livewire.php`:

```php
'make_command' => [
    'type' => 'class',
],
```

## Customizing Component Stubs

You can customize the files (or stubs) Livewire uses to generate new components by running:

```bash
php artisan livewire:stubs
```

This creates stub files in your application that you can modify:

**Single-file component stubs:**

- `stubs/livewire-sfc.stub` — Single-file components

**Multi-file component stubs:**

- `stubs/livewire-mfc-class.stub` — PHP class for multi-file components
- `stubs/livewire-mfc-view.stub` — Blade view for multi-file components
- `stubs/livewire-mfc-js.stub` — JavaScript for multi-file components
- `stubs/livewire-mfc-test.stub` — Pest test for multi-file components

**Class-based component stubs:**

- `stubs/livewire.stub` — PHP class for class-based components
- `stubs/livewire.view.stub` — Blade view for class-based components

**Additional stubs:**

- `stubs/livewire.attribute.stub` — Attribute classes
- `stubs/livewire.form.stub` — Form classes

Once published, Livewire will automatically use your custom stubs when generating new components.

## Troubleshooting

### Component Not Found

**Symptom:** Error message like "Component [post.create] not found" or "Unable to find component"

**Solutions:**

- Verify the component file exists at the expected path
- Check that the component name in your view matches the file structure (dots for subdirectories)
- For namespaced components, ensure the namespace is defined in `config/livewire.php` or manually registered in a service provider
- Try clearing your view cache: `php artisan view:clear`

### Component Shows Blank or Doesn't Render

**Common causes:**

- Missing root element in your Blade template (Livewire requires exactly one root element)
- Syntax errors in the PHP section of your component
- Check your Laravel logs for detailed error messages

### Class Name Conflicts

**Symptom:** Errors about duplicate class names when using single-file components

**Solution:** This can happen if you have multiple single-file components with the same name in different directories. Either:

- Rename one of the components to be unique
- Namespace one of the directories for more clear separation

## See Also

- [Properties](./properties.md) — Manage component state and data
- [Actions](./actions.md) — Handle user interactions with methods
- [Pages](./pages.md) — Use components as full pages with routing
- [Nesting](./nesting.md) — Compose components together and pass data between them
- [Lifecycle Hooks](./lifecycle-hooks.md) — Execute code at specific points in a component's lifecycle
