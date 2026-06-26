# Color Picker - PRO

Source: https://fluxui.dev/components/color-picker

Let users pick a color from a swatch palette, drag a saturation/value canvas, or paste a hex value. Perfect for theme settings, brand-color pickers, and any place users need to choose a color.

## Basic Example

```html
<flux:color-picker wire:model="theme" />
```

## Format

Pick the output format using the `format` prop. Alpha-aware formats (`hexa`, `rgba`, `hsla`) automatically render an alpha slider in the popover.

```html
<flux:color-picker format="hex" />
<flux:color-picker format="hexa" />
<flux:color-picker format="rgb" />
<flux:color-picker format="rgba" />
<flux:color-picker format="hsl" />
<flux:color-picker format="hsla" />
```

## Sizes

The color picker supports three sizes that match other form components.

```html
<flux:color-picker />
<flux:color-picker size="sm" />
<flux:color-picker size="xs" />
```

## Button Trigger

Use `type="button"` for a simpler trigger that displays the selected color and opens the popover on click. Useful for compact toolbars or swatches-only pickers where typing isn't needed.

```html
<flux:color-picker type="button" />
```

## Custom Swatches

Replace the default 24-color Tailwind palette with your own brand colors. Pass an array of hex strings, or `[value, label]` pairs for accessible swatch labels.

```html
<flux:color-picker :swatches="['#ef4444', '#22c55e', '#3b82f6', '#f59e0b', '#a855f7']" />

<!-- With [value, label] pairs for accessible swatch labels -->
<flux:color-picker :swatches="[['#ef4444', 'Red'], ['#22c55e', 'Green'], ['#3b82f6', 'Blue']]" />
```

## No Swatches

Pass `:swatches="false"` to hide the default swatch grid entirely. The popover will only show the SV canvas, hue slider, and hex input.

```html
<flux:color-picker :swatches="false" />
```

## Custom Layout

Compose `flux:color-picker.area`, `flux:color-picker.slider`, `flux:color-picker.input`, and `flux:color-picker.swatches` in any order to build a fully custom popover layout.

```html
<flux:color-picker>
    <div class="flex flex-col gap-3">
        <flux:color-picker.input placeholder="#000000" />
        <flux:separator />
        <flux:color-picker.area />
        <flux:color-picker.slider channel="hue" />
    </div>
</flux:color-picker>
```

## Dropper

Add the `dropper` prop to render a button that lets users pick a color from anywhere on the screen. Uses the browser's EyeDropper API — automatically disabled in unsupported browsers.

```html
<flux:color-picker dropper />
```

## Clearable

Add the `clearable` prop to render a clear (×) button on the trigger when a value is set.

```html
<flux:color-picker clearable />
```

## Copyable

Add the `copyable` prop to render a clipboard-copy button on the trigger. Only available with the default input trigger.

```html
<flux:color-picker copyable />
```

## Disabled

Prevent interaction and visually dim the trigger.

```html
<flux:color-picker disabled />
```

## Reference

### `flux:color-picker`

| Prop        | Description |
| ----------- | ----------- |
| wire:model  | Binds the color picker to a Livewire property. The value is a CSS color string in the configured format. |
| value       | Initial value as any CSS color string. Auto-normalized to the configured format on display. |
| name        | Generates a hidden form input with this name for native form submission (no `wire:model` required). |
| format      | Output format. Options: `hex` (default), `hexa`, `rgb`, `rgba`, `hsl`, `hsla`. Alpha-aware formats render an alpha slider automatically. |
| type        | Trigger type. Options: `input` (default), `button`. The button trigger shows the value and opens the popover on click; typing is not available. |
| placeholder | Placeholder text for the input trigger. Default: format-specific (e.g. `#000000` for hex). |
| size        | Trigger size. Options: `sm`, `xs`. Default: base. |
| :swatches   | Custom swatch palette as an array of hex strings or `[value, label]` pairs, or `false` to hide the default swatch grid entirely. |
| dropper     | Render a dropper button that lets users pick a color from the screen via the EyeDropper API. Automatically disabled in unsupported browsers. Default: false. |
| clearable   | Render a clear (×) button on the trigger when a value is set. Default: false. |
| copyable    | Render a clipboard-copy button on the trigger. Only available with the default input trigger. Default: false. |
| label       | Field label. Forwarded to `flux:with-field`. |
| description | Field description. Forwarded to `flux:with-field`. |
| disabled    | Disable the picker. Default: false. |
| invalid     | Mark the trigger as invalid. Auto-detected from the field's error bag when bound via `wire:model`. |

### `flux:color-picker.area`

| Prop  | Description |
| ----- | ----------- |
| class | Additional CSS classes applied to the saturation/value canvas element. |

### `flux:color-picker.slider`

| Prop    | Description |
| ------- | ----------- |
| channel | The color channel this slider controls. Options: `hue` (default), `alpha`. |

### `flux:color-picker.input`

| Prop        | Description |
| ----------- | ----------- |
| placeholder | Placeholder text for the text input. Default: format-specific (e.g. `#000000`). |

### `flux:color-picker.swatches`

| Prop  | Description |
| ----- | ----------- |
| class | Additional CSS classes applied to the swatches container. |

### `flux:color-picker.swatch`

| Prop  | Description |
| ----- | ----------- |
| value | The color value as a CSS color string (e.g. `#ef4444`). |
| label | Human-readable label used as the `aria-label` and hover tooltip. Defaults to the value. |

### `flux:color-picker.dropper`

| Prop  | Description |
| ----- | ----------- |
| class | Additional CSS classes applied to the dropper element. Automatically disabled in browsers that don't support the EyeDropper API. |
