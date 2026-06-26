# Synthesizers

**Source URL:** https://livewire.laravel.com/docs/4.x/synthesizers

## Overview

Because Livewire components are dehydrated (serialized) into JSON, then hydrated (unserialized) back into PHP components between requests, their properties need to be JSON-serializable.

While PHP serializes most primitive values easily, Livewire provides "Synthesizers"—a point of extension that allows you to support any custom property types (like models, collections, Carbon instances, and stringables).

## Understanding Synthesizers

Consider a CreatePost component with a stringable property:

```php
class CreatePost extends Component
{
    public $title = '';

    public function mount()
    {
        $this->title = str($this->title);
    }
}
```

The dehydrated JSON contains a metadata tuple:

```json
{
    "state": { "title": ["", { "s": "str" }] }
}
```

Here's Livewire's internal stringable synthesizer:

```php
use Illuminate\Support\Stringable;

class StringableSynth extends Synth
{
    public static $key = 'str';

    public static function match($target)
    {
        return $target instanceof Stringable;
    }

    public function dehydrate($target)
    {
        return [$target->__toString(), []];
    }

    public function hydrate($value)
    {
        return str($value);
    }
}
```

**Key components:**

- **$key:** Static property used to identify the synthesizer in metadata tuples
- **match():** Determines if this synthesizer should handle the property
- **dehydrate():** Converts PHP value to JSON-serializable tuple
- **hydrate():** Converts JSON back to PHP value

## Registering a Custom Synthesizer

To support a custom Address DTO:

```php
namespace App\Dtos\Address;

class Address
{
    public $street = '';
    public $city = '';
    public $state = '';
    public $zip = '';
}
```

Create a synthesizer:

```php
use App\Dtos\Address;

class AddressSynth extends Synth
{
    public static $key = 'address';

    public static function match($target)
    {
        return $target instanceof Address;
    }

    public function dehydrate($target)
    {
        return [[
            'street' => $target->street,
            'city' => $target->city,
            'state' => $target->state,
            'zip' => $target->zip,
        ], []];
    }

    public function hydrate($value)
    {
        $instance = new Address;

        $instance->street = $value['street'];
        $instance->city = $value['city'];
        $instance->state = $value['state'];
        $instance->zip = $value['zip'];

        return $instance;
    }
}
```

Register it in your service provider:

```php
class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::propertySynthesizer(AddressSynth::class);
    }
}
```

## Supporting Data Binding

To support `wire:model` binding to Address properties, add `get()` and `set()` methods:

```php
use App\Dtos\Address;

class AddressSynth extends Synth
{
    public static $key = 'address';

    public static function match($target)
    {
        return $target instanceof Address;
    }

    public function dehydrate($target)
    {
        return [[
            'street' => $target->street,
            'city' => $target->city,
            'state' => $target->state,
            'zip' => $target->zip,
        ], []];
    }

    public function hydrate($value)
    {
        $instance = new Address;

        $instance->street = $value['street'];
        $instance->city = $value['city'];
        $instance->state = $value['state'];
        $instance->zip = $value['zip'];

        return $instance;
    }

    public function get(&$target, $key)
    {
        return $target->{$key};
    }

    public function set(&$target, $key, $value)
    {
        $target->{$key} = $value;
    }
}
```

## See Also

- [Hydration](./hydration.md) — Understand component serialization
- [Properties](../essentials/properties.md) — Manage component state
