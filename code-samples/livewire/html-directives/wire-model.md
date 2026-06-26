# wire:model

**Source URL:** https://livewire.laravel.com/docs/4.x/wire-model

## Overview

Livewire makes it easy to bind a component property's value with form inputs using `wire:model`.

Here is a simple example of using `wire:model` to bind the `$title` and `$content` properties with form inputs in a "Create Post" component:

```php
use Livewire\Component;
use App\Models\Post;

class CreatePost extends Component
{
    public $title = '';

    public $content = '';

    public function save()
    {
        $post = Post::create([
            'title' => $this->title,
            'content' => $this->content
        ]);

        // ...
    }
}
```

```html
<form wire:submit="save">
    <label>
        <span>Title</span>

        <input type="text" wire:model="title">
    </label>

    <label>
        <span>Content</span>

        <textarea wire:model="content"></textarea>
    </label>

    <button type="submit">Save</button>
</form>
```

Because both inputs use `wire:model`, their values will be synchronized with the server's properties when the "Save" button is pressed.

**Note:** If you tried this in your browser and are confused why the title isn't automatically updating, it's because Livewire only updates a component when an "action" is submitted—like pressing a submit button—not when a user types into a field. This cuts down on network requests and improves performance. To enable "live" updating as a user types, you can use `wire:model.live` instead.

## Customizing Update Timing

By default, Livewire will only send a network request when an action is performed (like `wire:click` or `wire:submit`), NOT when a `wire:model` input is updated.

This drastically improves the performance of Livewire by reducing network requests and provides a smoother experience for your users.

However, there are occasions where you may want to update the server more frequently for things like real-time validation.

### Live Updating

To send property updates to the server as a user types into an input-field, you can append the `.live` modifier to `wire:model`:

```html
<input type="text" wire:model.live="title">
```

### Customizing the Debounce

By default, when using `wire:model.live`, Livewire adds a 150 millisecond debounce to server updates. This means if a user is continually typing, Livewire will wait until the user stops typing for 150 milliseconds before sending a request.

You can customize this timing by appending `.debounce.Xms` after `.live`. Here is an example of changing the debounce to 250 milliseconds:

```html
<input type="text" wire:model.live.debounce.250ms="title">
```

### Updating on "blur" Event

The `.blur` modifier delays syncing until the user clicks away from the input:

```html
<input type="text" wire:model.blur="title">
```

To also send a network request on blur, add `.live`:

```html
<input type="text" wire:model.blur.live="title">
```

### Updating on "change" Event

The `.change` modifier triggers on the change event, which is useful for select elements:

```html
<select wire:model.change="state">...</select>

<!-- With network request -->
<select wire:model.change.live="state">...</select>
```

### Updating on "enter" Key

The `.enter` modifier syncs when the user presses the Enter key:

```html
<input type="text" wire:model.enter="search">

<!-- With network request -->
<input type="text" wire:model.enter.live="search">
```

## Input Fields

Livewire supports most native input elements out of the box. Here's a comprehensive list of the different available input types:

### Text Inputs

```html
<input type="text" wire:model="title">
```

### Textarea Inputs

```html
<textarea wire:model="content"></textarea>
```

If the "content" value is initialized with a string, Livewire will fill the textarea with that value.

### Single Checkbox

```html
<input type="checkbox" wire:model="receiveUpdates">
```

When the `$receiveUpdates` value is false, the checkbox will be unchecked. When true, it will be checked.

### Multiple Checkboxes

```php
public $updateTypes = [];
```

```html
<input type="checkbox" value="email" wire:model="updateTypes">
<input type="checkbox" value="sms" wire:model="updateTypes">
<input type="checkbox" value="notification" wire:model="updateTypes">
```

If the user checks the first two boxes, `$updateTypes` will be: `["email", "sms"]`

### Radio Buttons

```html
<input type="radio" value="yes" wire:model="receiveUpdates">
<input type="radio" value="no" wire:model="receiveUpdates">
```

### Select Dropdowns

```html
<select wire:model="state">
    <option value="AL">Alabama</option>
    <option value="AK">Alaska</option>
    <option value="AZ">Arizona</option>
</select>
```

When a specific state is selected, the `$state` property will be set to the selected value.

### Dynamic Select Options

```html
<select wire:model="state">
    @foreach (\App\Models\State::all() as $state)
        <option value="{{ $state->id }}">{{ $state->label }}</option>
    @endforeach
</select>
```

### Placeholder Option

```html
<select wire:model="state">
    <option disabled value="">Select a state...</option>

    @foreach (\App\Models\State::all() as $state)
        <option value="{{ $state->id }}">{{ $state->label }}</option>
    @endforeach
</select>
```

### Dependent Select Dropdowns

When one select menu depends on another, add `wire:key` to the dependent select:

```html
<!-- States select menu... -->
<select wire:model.live="selectedState">
    @foreach (State::all() as $state)
        <option value="{{ $state->id }}">{{ $state->label }}</option>
    @endforeach
</select>

<!-- Cities dependent select menu... -->
<select wire:model.live="selectedCity" wire:key="{{ $selectedState }}">
    @foreach (City::whereStateId($selectedState->id)->get() as $city)
        <option value="{{ $city->id }}">{{ $city->label }}</option>
    @endforeach
</select>
```

### Multi-Select Dropdowns

```html
<select wire:model="states" multiple>
    <option value="AL">Alabama</option>
    <option value="AK">Alaska</option>
    <option value="AZ">Arizona</option>
</select>
```

## Event Propagation

By default, `wire:model` only listens for input/change events that originate directly on the element itself, not events that bubble up from child elements.

### Listening to Child Events

In rare cases where you want `wire:model` to also respond to events bubbling up from child elements, you can use the `.deep` modifier:

```html
<div wire:model.deep="value">
    <input type="text"> <!-- Changes here will update $value -->
</div>
```

**Note:** Use `.deep` sparingly. Most use cases don't require listening to child events.

## Reference

**Syntax:** `wire:model="propertyName"`

## Modifiers

| Modifier | Description |
|----------|-------------|
| `.live` | Send updates to the server |
| `.blur` | Only update on blur |
| `.change` | Only update on change |
| `.enter` | Only update on enter key |
| `.lazy` | Update on change and send network request (v3 compatible) |
| `.debounce.Xms` | Debounce updates (use with `.live`) |
| `.throttle.Xms` | Throttle updates (use with `.live`) |
| `.number` | Cast value to int on the server |
| `.boolean` | Cast value to bool on the server |
| `.fill` | Use initial value from HTML value attribute |
| `.deep` | Also listen to events from child elements |
| `.preserve-scroll` | Maintain scroll position during updates |

## See Also

- [Forms](../essentials/forms.md) — Complete guide to building forms with Livewire
- [Properties](../essentials/properties.md) — Understand data binding and property management
- [Validation](../features/validation.md) — Validate bound properties in real-time
- [File Uploads](../features/uploads.md) — Bind file inputs with wire:model
