# Text

Source: https://fluxui.dev/components/text

Consistent typographical components like text and link.

## Basic Example

```html
<flux:heading>Text component</flux:heading>
<flux:text class="mt-2">This is the standard text component for body copy and general content throughout your application.</flux:text>
```

## Size

```html
<flux:text class="text-base">Base text size</flux:text>
<flux:text>Default text size</flux:text>
<flux:text class="text-xs">Smaller text</flux:text>
```

## Color

```html
<flux:text variant="strong">Strong text color</flux:text>
<flux:text>Default text color</flux:text>
<flux:text variant="subtle">Subtle text color</flux:text>
<flux:text color="blue">Colored text</flux:text>
```

## Link

```html
<flux:text>Visit our <flux:link href="#">documentation</flux:link> for more information.</flux:text>
```

## Link variants

```html
<flux:link href="#">Default link</flux:link>
<flux:link href="#" variant="ghost">Ghost link</flux:link>
<flux:link href="#" variant="subtle">Subtle link</flux:link>
```

## Link as button

```html
<flux:link as="button" wire:click="...">Create new account →</flux:link>
```

## Reference

### `flux:text`

| Prop | Description |
| --- | --- |
| size | Size of the text. Options: sm, default, lg, xl. Default: default. |
| variant | Text variant. Options: strong, subtle. Default: default. |
| color | Color of the text. Options: default, red, orange, yellow, lime, green, emerald, teal, cyan, sky, blue, indigo, violet, purple, fuchsia, pink, rose. Default: default. |
| inline | If true, the text element will be a span instead of a p. |

| Slot | Description |
| --- | --- |
| default | The text content. |

### `flux:link`

| Prop | Description |
| --- | --- |
| href | The URL that the link points to. Required. |
| variant | Link style variant. Options: default, ghost, subtle. Default: default. |
| external | If true, the link will open in a new tab. |
| as | The HTML tag to render the link as. Options: a (default), button. |

| Slot | Description |
| --- | --- |
| default | The link content. |
