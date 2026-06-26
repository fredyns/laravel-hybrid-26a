# Validation

**Source URL:** https://livewire.laravel.com/docs/4.x/validation

## Overview

Livewire aims to make validating a user's input and giving them feedback as pleasant as possible. By building on top of Laravel's validation features, Livewire leverages your existing knowledge while also providing you with robust, additional features like real-time validation.

## Basic Validation

Here's an example CreatePost component that demonstrates the most basic validation workflow in Livewire:

```php
<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Post;

class CreatePost extends Component
{
    public $title = '';

    public $content = '';

    public function save()
    {
        $validated = $this->validate([ 
            'title' => 'required|min:3',
            'content' => 'required|min:3',
        ]);

        Post::create($validated);

        return redirect()->to('/posts');
    }

    public function render()
    {
        return view('livewire.create-post');
    }
}
```

```html
<form wire:submit="save">
    <input type="text" wire:model="title">
    <div>@error('title') {{ $message }} @enderror</div>

    <textarea wire:model="content"></textarea>
    <div>@error('content') {{ $message }} @enderror</div>

    <button type="submit">Save</button>
</form>
```

Livewire provides a `validate()` method that you can call to validate your component's properties. It returns the validated set of data that you can then safely insert into the database.

On the frontend, you can use Laravel's existing Blade directives to show validation messages to your users.

## Validate Attributes

If you prefer to co-locate your component's validation rules with the properties directly, you can use Livewire's `#[Validate]` attribute.

By associating validation rules with properties using `#[Validate]`, Livewire will automatically run the properties validation rules before each update. However, you should still run `$this->validate()` before persisting data to a database so that properties that haven't been updated are also validated.

```php
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\Post;

class CreatePost extends Component
{
    #[Validate('required|min:3')] 
    public $title = '';

    #[Validate('required|min:3')] 
    public $content = '';

    public function save()
    {
        $this->validate();

        Post::create([
            'title' => $this->title,
            'content' => $this->content,
        ]);

        return redirect()->to('/posts');
    }

    // ...
}
```

**Important:** Validate attributes don't support Rule objects. PHP Attributes are restricted to certain syntaxes like plain strings and arrays. If you find yourself wanting to use run-time syntaxes like Laravel's Rule objects (`Rule::exists(...)`), you should instead define a `rules()` method in your component.

### Disabling Automatic Validation

If you prefer more control over when the properties are validated, you can pass an `onUpdate: false` parameter to the `#[Validate]` attribute. This will disable any automatic validation and instead assume you want to manually validate the properties using the `$this->validate()` method:

```php
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\Post;

class CreatePost extends Component
{
    #[Validate('required|min:3', onUpdate: false)]
    public $title = '';

    #[Validate('required|min:3', onUpdate: false)]
    public $content = '';

    public function save()
    {
        $validated = $this->validate();

        Post::create($validated);

        return redirect()->to('/posts');
    }

    // ...
}
```

## Custom Attribute Name

If you wish to customize the attribute name injected into the validation message, you may do so using the `as:` parameter:

```php
use Livewire\Attributes\Validate;

#[Validate('required', as: 'date of birth')]
public $dob;
```

When validation fails in the above snippet, Laravel will use "date of birth" instead of "dob" as the name of the field in the validation message. The generated message will be "The date of birth field is required" instead of "The dob field is required".

## Custom Validation Message

To bypass Laravel's validation message and replace it with your own, you can use the `message:` parameter in the `#[Validate]` attribute:

```php
use Livewire\Attributes\Validate;

#[Validate('required', message: 'Please provide a post title')]
public $title;
```

Now, when the validation fails for this property, the message will be "Please provide a post title" instead of "The title field is required".

If you wish to add different messages for different rules, you can simply provide multiple `#[Validate]` attributes:

```php
#[Validate('required', message: 'Please provide a post title')]
#[Validate('min:3', message: 'This title is too short')]
public $title;
```

## Opting Out of Localization

By default, Livewire rule messages and attributes are localized using Laravel's translate helper: `trans()`.

You can opt-out of localization by passing the `translate: false` parameter to the `#[Validate]` attribute:

```php
#[Validate('required', message: 'Please provide a post title', translate: false)]
public $title;
```

## Custom Validation Key

When applying validation rules directly to a property using the `#[Validate]` attribute, Livewire assumes the validation key should be the name of the property itself. However, there are times when you may want to customize the validation key.

For example, you might want to provide separate validation rules for an array property and its children. In this case, instead of passing a validation rule as the first argument to the `#[Validate]` attribute, you can pass an array of key-value pairs instead:

```php
#[Validate([
    'todos' => 'required',
    'todos.*' => [
        'required',
        'min:3',
        new Uppercase,
    ],
])]
public $todos = [];
```

Now, when a user updates `$todos`, or the `validate()` method is called, both of these validation rules will be applied.

## Form Objects

As more properties and validation rules are added to a Livewire component, it can begin to feel too crowded. To alleviate this pain and also provide a helpful abstraction for code reuse, you can use Livewire's Form Objects to store your properties and validation rules.

Below is the same CreatePost example, but now the properties and rules have been extracted to a dedicated form object named PostForm:

```php
<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class PostForm extends Form
{
    #[Validate('required|min:3')]
    public $title = '';

    #[Validate('required|min:3')]
    public $content = '';
}
```

The PostForm above can now be defined as a property on the CreatePost component:

```php
<?php

namespace App\Livewire;

use App\Livewire\Forms\PostForm;
use Livewire\Component;
use App\Models\Post;

class CreatePost extends Component
{
    public PostForm $form;

    public function save()
    {
        Post::create(
            $this->form->all()
        );

        return redirect()->to('/posts');
    }

    // ...
}
```

## Real-Time Validation

By default, validation only runs when you explicitly call `$this->validate()`. However, you can enable real-time validation by using the `.live` modifier on your `wire:model` directives:

```html
<input type="text" wire:model.live="title">
@error('title') <span>{{ $message }}</span> @enderror
```

Now, as a user types into the input, the validation rules will run automatically and display any errors in real-time.

## See Also

- [Forms](./forms.md) — Handle form submissions with validation
- [Properties](../essentials/properties.md) — Manage component state
- [Actions](../essentials/actions.md) — Trigger component methods
