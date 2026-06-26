# Badge

Source: https://fluxui.dev/components/badge

Highlight information like status, category, or count.

## Basic Example

```html
<flux:badge color="lime">New</flux:badge>
```

## Sizes

```html
<flux:badge size="sm">Small</flux:badge>
<flux:badge>Default</flux:badge>
<flux:badge size="lg">Large</flux:badge>
```

## Icons

```html
<flux:badge icon="user-circle">Users</flux:badge>
<flux:badge icon="document-text">Files</flux:badge>
<flux:badge icon:trailing="video-camera">Videos</flux:badge>
```

## Rounded

```html
<flux:badge rounded icon="user">Users</flux:badge>
```

## As button

```html
<flux:badge as="button" rounded icon="plus" size="lg">Amount</flux:badge>
```

## With close button

```html
<flux:badge>
    Admin <flux:badge.close />
</flux:badge>
```

## Colors

```html
<flux:badge color="zinc">Zinc</flux:badge>
<flux:badge color="red">Red</flux:badge>
<flux:badge color="orange">Orange</flux:badge>
<flux:badge color="amber">Amber</flux:badge>
<flux:badge color="yellow">Yellow</flux:badge>
<flux:badge color="lime">Lime</flux:badge>
<flux:badge color="green">Green</flux:badge>
<flux:badge color="emerald">Emerald</flux:badge>
<flux:badge color="teal">Teal</flux:badge>
<flux:badge color="cyan">Cyan</flux:badge>
<flux:badge color="sky">Sky</flux:badge>
<flux:badge color="blue">Blue</flux:badge>
<flux:badge color="indigo">Indigo</flux:badge>
<flux:badge color="violet">Violet</flux:badge>
<flux:badge color="purple">Purple</flux:badge>
<flux:badge color="fuchsia">Fuchsia</flux:badge>
<flux:badge color="pink">Pink</flux:badge>
<flux:badge color="rose">Rose</flux:badge>
```

## Solid variant

```html
<flux:badge variant="solid" color="zinc">Zinc</flux:badge>
<flux:badge variant="solid" color="red">Red</flux:badge>
<flux:badge variant="solid" color="orange">Orange</flux:badge>
<flux:badge variant="solid" color="amber">Amber</flux:badge>
<flux:badge variant="solid" color="yellow">Yellow</flux:badge>
<flux:badge variant="solid" color="lime">Lime</flux:badge>
<flux:badge variant="solid" color="green">Green</flux:badge>
<flux:badge variant="solid" color="emerald">Emerald</flux:badge>
<flux:badge variant="solid" color="teal">Teal</flux:badge>
<flux:badge variant="solid" color="cyan">Cyan</flux:badge>
<flux:badge variant="solid" color="sky">Sky</flux:badge>
<flux:badge variant="solid" color="blue">Blue</flux:badge>
<flux:badge variant="solid" color="indigo">Indigo</flux:badge>
<flux:badge variant="solid" color="violet">Violet</flux:badge>
<flux:badge variant="solid" color="purple">Purple</flux:badge>
<flux:badge variant="solid" color="fuchsia">Fuchsia</flux:badge>
<flux:badge variant="solid" color="pink">Pink</flux:badge>
<flux:badge variant="solid" color="rose">Rose</flux:badge>
```

## Inset

```html
<flux:heading>
    Page builder <flux:badge color="lime" inset="top bottom">New</flux:badge>
</flux:heading>
<flux:text class="mt-2">Easily author new pages without leaving your browser.</flux:text>
```

## Reference

### `flux:badge`

| Prop | Description |
| --- | --- |
| color | Badge color (e.g., zinc, red, blue). Default: zinc. |
| size | Badge size. Options: sm, lg. |
| rounded | If present or true, makes the badge fully rounded instead of slightly rounded corners. |
| variant | Badge style variant. Options: solid (depracated: pill, use rounded prop instead). |
| icon | Name of the icon to display before the badge text. |
| icon:trailing | Name of the icon to display after the badge text. |
| icon:variant | Icon variant. Options: outline, solid, mini, micro. Default: mini. |
| as | HTML element to render the badge as. Options: button. Default: div. |
| inset | Add negative margins to specific sides. Options: top, bottom, left, right, or any combination of the four. |

### `flux:badge.close`

| Prop | Description |
| --- | --- |
| icon | Name of the icon to display. Default: x-mark. |
| icon:variant | Icon variant. Options: outline, solid, mini, micro. Default: mini. |
