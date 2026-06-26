# Icon

Source: https://fluxui.dev/components/icon

Flux uses the excellent Heroicons project for its icon collection. Heroicons is a set of beautiful and functional icons crafted by the fine folks at Tailwind Labs

## Basic Example

```html
<flux:icon.bolt />
```

## Variants

```html
<flux:icon.bolt />
<!-- 24px, outline -->

<flux:icon.bolt variant="solid" />
<!-- 24px, filled -->

<flux:icon.bolt variant="mini" />
<!-- 20px, filled -->

<flux:icon.bolt variant="micro" />
<!-- 16px, filled -->
```

## Sizes

```html
<flux:icon.bolt class="size-12" />
<flux:icon.bolt class="size-10" />
<flux:icon.bolt class="size-8" />
```

## Color

```html
<flux:icon.bolt variant="solid" class="text-amber-500 dark:text-amber-300" />
```

## Loading spinner

```html
<flux:icon.loading />
```

## Dynamic icons

```html
<flux:icon name="bolt" />
```

## Lucide icons

```html
php artisan flux:icon
```

```html
php artisan flux:icon crown grip-vertical github
```

```html
<flux:icon.crown />
<flux:icon.grip-vertical />
<flux:icon.github />
```

## Custom icons

```html
- resources
- views
- flux
- icon
- wink.blade.php
```

```html
@php $attributes = $unescapedForwardedAttributes ?? $attributes; @endphp

@props([
    'variant' => 'outline',
])

@php
    $classes = Flux::classes('shrink-0')
        ->add(match($variant) {
            'outline' => '[:where(&)]:size-6',
            'solid' => '[:where(&)]:size-6',
            'mini' => '[:where(&)]:size-5',
            'micro' => '[:where(&)]:size-4',
        });
@endphp

{{-- Your SVG code here: --}}
<svg {{ $attributes->class($classes) }} data-flux-icon aria-hidden="true" ... >
    ...
</svg>
```

```html
<flux:icon.wink />
```

## Reference

### `flux:icon.*`

| Prop | Description |
| --- | --- |
| variant | Visual style of the icon. Options: outline (default), solid, mini, micro. |

| Class | Description |
| --- | --- |
| size-* | Control the size of the icon using Tailwind's size utilities (e.g., `size-8`, `size-12`). |
| text-* | Control the color of the icon using Tailwind's text color utilities (e.g., `text-blue-500`). |

| Attribute | Description |
| --- | --- |
| data-flux-icon | Applied to the root SVG element for styling and identification. |

### `Icon sizes`

| Size | Description |
| --- | --- |
| outline | 24x24 pixels (default) |
| solid | 24x24 pixels |
| mini | 20x20 pixels |
| micro | 16x16 pixels |
