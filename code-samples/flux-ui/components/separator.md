# Separator

Source: https://fluxui.dev/components/separator

Visually divide sections of content or groups of items.

## Basic Example

```html
<flux:separator />
```

## With text

```html
<flux:separator text="or" />
```

## Vertical

```html
<flux:separator vertical />
```

## Limited height

```html
<flux:separator vertical class="my-2" />
```

## Subtle

```html
<flux:separator vertical variant="subtle" />
```

## Reference

### `flux:separator`

| Prop | Description |
| --- | --- |
| vertical | Displays a vertical separator. Default is horizontal. |
| variant | Visual style variant. Options: subtle. Default: standard separator. |
| text | Optional text to display in the center of the separator. |
| orientation | Alternative to vertical prop. Options: horizontal, vertical. Default: horizontal. |

| Slot | Description |
| --- | --- |
| default | Custom content to display in the center of the separator. |

| Attribute | Description |
| --- | --- |
| data-flux-separator | Applied to the root element for styling and identification. |
