# Forms

**Source URL:** https://livewire.laravel.com/docs/4.x/forms

## Overview

Because forms are the backbone of most web applications, Livewire provides loads of helpful utilities for building them. From handling simple input elements to complex things like real-time validation or file uploading, Livewire has simple, well-documented tools to make your life easier and delight your users.

## Submitting a Form

Let's start by looking at a very simple form in a `post.create` component. This form will have two simple text inputs and a submit button, as well as some code on the backend to manage the form's state and submission:

```php
<?php // resources/views/components/post/⚡create.blade.php

use Livewire\Component;
use App\Models\Post;

new class extends Component {
    public $title = '';

    public $content = '';

    public function save()
    {
        Post::create(
            $this->only(['title', 'content'])
        );

        session()->flash('status', 'Post successfully updated.');

        return $this->redirect('/posts');
    }
};
?>

<form wire:submit="save">
    <input type="text" wire:model="title">

    <input type="text" wire:model="content">

    <button type="submit">Save</button>
</form>
```

As you can see, we are "binding" the public `$title` and `$content` properties in the form above using `wire:model`. This is one of the most commonly used and powerful features of Livewire.

In addition to binding `$title` and `$content`, we are using `wire:submit` to capture the submit event when the "Save" button is clicked and invoking the `save()` action. This action will persist the form input to the database.

After the new post is created in the database, we redirect the user to the posts page and show them a "flash" message that the new post was created.

## Adding Validation

To avoid storing incomplete or dangerous user input, most forms need some sort of input validation.

Livewire makes validating your forms as simple as adding `#[Validate]` attributes above the properties you want to be validated.

Once a property has a `#[Validate]` attribute attached to it, the validation rule will be applied to the property's value any time it's updated server-side.

### Example: Adding Validation Rules

```php
<?php // resources/views/components/post/⚡create.blade.php

use Livewire\Attributes\Validate; 
use Livewire\Component;
use App\Models\Post;

new class extends Component {
    #[Validate('required')] 
    public $title = '';

    #[Validate('required')] 
    public $content = '';

    public function save()
    {
        $this->validate(); 

        Post::create(
            $this->only(['title', 'content'])
        );

        return $this->redirect('/posts');
    }
};
```

We'll also modify our Blade template to show any validation errors on the page:

```html
<form wire:submit="save">
    <input type="text" wire:model="title">
    <div>
        @error('title') <span class="error">{{ $message }}</span> @enderror 
    </div>

    <input type="text" wire:model="content">
    <div>
        @error('content') <span class="error">{{ $message }}</span> @enderror 
    </div>

    <button type="submit">Save</button>
</form>
```

Now, if the user tries to submit the form without filling in any of the fields, they will see validation messages telling them which fields are required before saving the post.

## Extracting a Form Object

If you are working with a large form and prefer to extract all of its properties, validation logic, etc., into a separate class, Livewire offers form objects.

Form objects allow you to re-use form logic across components and provide a nice way to keep your component class cleaner by grouping all form-related code into a separate class.

You can either create a form class by hand or use the convenient artisan command:

```bash
php artisan livewire:form PostForm
```

The above command will create a file called `app/Livewire/Forms/PostForm.php`.

### Creating a Form Object

```php
<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class PostForm extends Form
{
    #[Validate('required|min:5')]
    public $title = '';

    #[Validate('required|min:5')]
    public $content = '';
}
```

### Using a Form Object in a Component

```php
<?php // resources/views/components/post/⚡create.blade.php

use App\Livewire\Forms\PostForm;
use Livewire\Component;
use App\Models\Post;

new class extends Component {
    public PostForm $form; 

    public function save()
    {
        $this->validate();

        Post::create(
            $this->form->only(['title', 'content']) 
        );

        return $this->redirect('/posts');
    }
};
```

```html
<form wire:submit="save">
    <input type="text" wire:model="form.title">
    <div>
        @error('form.title') <span class="error">{{ $message }}</span> @enderror
    </div>

    <input type="text" wire:model="form.content">
    <div>
        @error('form.content') <span class="error">{{ $message }}</span> @enderror
    </div>

    <button type="submit">Save</button>
</form>
```

### Extracting Business Logic to Form Objects

If you'd like, you can also extract the post creation logic into the form object like so:

```php
<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use App\Models\Post;
use Livewire\Form;

class PostForm extends Form
{
    #[Validate('required|min:5')]
    public $title = '';

    #[Validate('required|min:5')]
    public $content = '';

    public function store() 
    {
        $this->validate();

        Post::create($this->only(['title', 'content']));
    }
}
```

Now you can call `$this->form->store()` from the component:

```php
<?php // resources/views/components/post/⚡create.blade.php

use App\Livewire\Forms\PostForm;
use Livewire\Component;

new class extends Component {
    public PostForm $form;

    public function save()
    {
        $this->form->store(); 

        return $this->redirect('/posts');
    }

    // ...
};
```

### Using Form Objects for Create and Update

If you want to use this form object for both a create and update form, you can easily adapt it to handle both use cases.

Here's what it would look like to use this same form object for a `post.edit` component and fill it with initial data:

```php
<?php // resources/views/components/post/⚡edit.blade.php

use App\Livewire\Forms\PostForm;
use Livewire\Component;
use App\Models\Post;

new class extends Component {
    public PostForm $form;

    public function mount(Post $post)
    {
        $this->form->setPost($post);
    }

    public function save()
    {
        $this->form->update();

        return $this->redirect('/posts');
    }
};
```

```php
<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\Post;

class PostForm extends Form
{
    public ?Post $post;

    #[Validate('required|min:5')]
    public $title = '';

    #[Validate('required|min:5')]
    public $content = '';

    public function setPost(Post $post)
    {
        $this->post = $post;

        $this->title = $post->title;

        $this->content = $post->content;
    }

    public function store()
    {
        $this->validate();

        Post::create($this->only(['title', 'content']));
    }

    public function update()
    {
        $this->validate();

        $this->post->update(
            $this->only(['title', 'content'])
        );
    }
}
```

As you can see, we've added a `setPost()` method to the `PostForm` object to optionally allow for filling the form with existing data as well as storing the post on the form object for later use. We've also added an `update()` method for updating the existing post.

Form objects are not required when working with Livewire, but they do offer a nice abstraction for keeping your components free of repetitive boilerplate.

## Resetting Form Fields

If you are using a form object, you may want to reset the form after it has been submitted. This can be done by calling the `reset()` method:

```php
<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use App\Models\Post;
use Livewire\Form;

class PostForm extends Form
{
    #[Validate('required|min:5')]
    public $title = '';

    #[Validate('required|min:5')]
    public $content = '';

    // ...

    public function store()
    {
        $this->validate();

        Post::create($this->only(['title', 'content']));

        $this->reset(); 
    }
}
```

You can also reset specific properties by passing the property names into the `reset()` method:

```php
$this->reset('title');

// Or multiple at once...

$this->reset('title', 'content');
```

## Real-Time Form Saving

In some cases, you may want to save form data to the database as the user types, rather than waiting for them to submit a form. This is sometimes called "auto-save" functionality.

### Example: Real-Time Saving with Updated Hook

```php
<?php // resources/views/components/post/⚡edit.blade.php

use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\Post;

new class extends Component {
    public Post $post;

    #[Validate('required')]
    public $title = '';

    #[Validate('required')]
    public $content = '';

    public function mount(Post $post)
    {
        $this->post = $post;
        $this->title = $post->title;
        $this->content = $post->content;
    }

    public function updated($name, $value) 
    {
        $this->post->update([
            $name => $value,
        ]);
    }
};
?>

<form wire:submit>
    <input type="text" wire:model.live.blur="title">
    <div>
        @error('title') <span class="error">{{ $message }}</span> @enderror
    </div>

    <input type="text" wire:model.live.blur="content">
    <div>
        @error('content') <span class="error">{{ $message }}</span> @enderror
    </div>
</form>
```

In the above example, when a user completes a field (by clicking or tabbing to the next field), a network request is sent to update that property on the component. Immediately after the property is updated on the class, the `updated()` hook is called for that specific property name and its new value.

We can use this hook to update only that specific field in the database.

Additionally, because we have the `#[Validate]` attributes attached to those properties, the validation rules will be run before the property is updated and the `updated()` hook is called.

## Showing Dirty Indicators

In the real-time saving scenario discussed above, it may be helpful to indicate to users when a field hasn't been persisted to the database yet.

For example, if a user visits a `post.edit` page and starts modifying the title of the post in a text input, it may be unclear to them when the title is actually being updated in the database, especially if there is no "Save" button at the bottom of the form.

Livewire provides the `wire:dirty` directive to allow you to toggle elements or modify classes when an input's value diverges from the server-side component:

```html
<input type="text" wire:model.live.blur="title" wire:dirty.class="border-yellow">
```

In the above example, when a user types into the input field, a yellow border will appear around the field. When the user tabs away, the network request is sent and the border will disappear; signaling to them that the input has been persisted and is no longer "dirty".

If you want to toggle an entire element's visibility, you can do so by using `wire:dirty` in conjunction with `wire:target`. `wire:target` is used to specify which piece of data you want to watch for "dirtiness". In this case, the "title" field:

```html
<input type="text" wire:model="title">

<div wire:dirty wire:target="title">Unsaved...</div>
```

## Debouncing Input

When using `.live` on a text input, you may want more fine-grained control over how often a network request is sent. By default, a debounce of "250ms" is applied to the input; however, you can customize this using the `.debounce` modifier:

```html
<input type="text" wire:model.live.debounce.150ms="title" >
```

Now that `.debounce.150ms` has been added to the field, a shorter debounce of "150ms" will be used when handling input updates for this field. In other words, as a user types, a network request will only be sent if the user stops typing for at least 150 milliseconds.

## Throttling Input

As stated previously, when an input debounce is applied to a field, a network request will not be sent until the user has stopped typing for a certain amount of time. This means if the user continues typing a long message, a network request won't be sent until the user is finished.

Sometimes this isn't the desired behavior, and you would rather send a request as the user types, not when they've finished or taken a break.

In these cases, you can instead use `.throttle` to signify a time interval to send network requests:

```html
<input type="text" wire:model.live.throttle.150ms="title" >
```

In the above example, as a user is typing continuously in the "title" field, a network request will be sent every 150 milliseconds until the user is finished.

## Extracting Input Fields to Blade Components

Even in a small component such as the `post.create` example we've been discussing, we end up duplicating lots of form field boilerplate like validation messages and labels.

It can be helpful to extract repetitive UI elements such as these into dedicated Blade components to be shared across your application.

### Example: Creating a Reusable Input Component

Original template:

```html
<form wire:submit="save">
    <input type="text" wire:model="title"> 
    <div>
        @error('title') <span class="error">{{ $message }}</span> @enderror
    </div>

    <input type="text" wire:model="content"> 
    <div>
        @error('content') <span class="error">{{ $message }}</span> @enderror
    </div>

    <button type="submit">Save</button>
</form>
```

Refactored with Blade component:

```html
<form wire:submit="save">
    <x-input-text name="title" wire:model="title" /> 

    <x-input-text name="content" wire:model="content" /> 

    <button type="submit">Save</button>
</form>
```

Blade component source:

```html
<!-- resources/views/components/input-text.blade.php -->

@props(['name'])

<input type="text" name="{{ $name }}" {{ $attributes }}>

<div>
    @error($name) <span class="error">{{ $message }}</span> @enderror
</div>
```

By specifying `name` as a "prop" using `@props(['name'])` we are telling Blade: if an attribute called "name" is set on this component, take its value and make it available inside this component as `$name`.

For other attributes that don't have an explicit purpose, we used the `{{ $attributes }}` statement. This is used for "attribute forwarding", or in other words, taking any HTML attributes written on the Blade component and forwarding them onto an element within the component.

This ensures `wire:model="title"` and any other extra attributes such as `disabled`, `class="..."`, or `required` still get forwarded to the actual `<input>` element.

## Custom Form Controls

In the previous example, we "wrapped" an input element into a re-usable Blade component we can use as if it was a native HTML input element.

This pattern is very useful; however, there might be some cases where you want to create an entire input component from scratch (without an underlying native input element), but still be able to bind its value to Livewire properties using `wire:model`.

### Example: Counter Input Component

Pure Alpine counter component:

```html
<div x-data="{ count: 0 }">
    <button x-on:click="count--">-</button>

    <span x-text="count"></span>

    <button x-on:click="count++">+</button>
</div>
```

To make this work with `wire:model="quantity"`:

```html
<!-- resources/view/components/input-counter.blade.php -->

<div x-data="{ count: 0 }" x-modelable="count" {{ $attributes}}>
    <button x-on:click="count--">-</button>

    <span x-text="count"></span>

    <button x-on:click="count++">+</button>
</div>
```

`x-modelable` is a utility in Alpine that tells Alpine to make a certain piece of data available for binding from outside. When the HTML is rendered in the browser, `wire:model="quantity"` will be rendered alongside `x-modelable="count"` on the root `<div>` of the Alpine component.

`x-modelable="count"` tells Alpine to look for any `x-model` or `wire:model` statements and use "count" as the data to bind them to.

Because `x-modelable` works for both `wire:model` and `x-model`, you can also use this Blade component interchangeably with Livewire and Alpine:

```html
<x-input-counter x-model="quantity" />
```

## See Also

- [Validation](../features/validation.md) — Validate form inputs with real-time feedback
- [wire:model](../html-directives/wire-model.md) — Bind form inputs to component properties
- [File Uploads](../features/uploads.md) — Handle file uploads in forms
- [Actions](./actions.md) — Process form submissions with actions
- [Loading States](../features/loading-states.md) — Show loading indicators during form submission
