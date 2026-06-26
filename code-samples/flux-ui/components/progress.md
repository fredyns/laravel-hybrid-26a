# Progress

Source: https://fluxui.dev/components/progress

A simple progress bar to indicate completion or loading status.

## Basic Example

```html
<flux:progress value="75" />
```

## Max

```html
<flux:progress value="3" max="7" />
```

## Color

```html
<flux:progress value="75" color="purple" />
```

## Height

```html
<flux:progress value="75" class="h-3" />
```

## With label

```html
<flux:field>
    <flux:label>Upload progress</flux:label>

    <flux:progress value="42" color="blue" />

    <flux:description>Uploading 3 of 7 files...</flux:description>
</flux:field>
```

## Display value

```html
<flux:field>
    <flux:label>
        Storage

        <x-slot name="trailing">
            <span class="tabular-nums">75%</span>
        </x-slot>
    </flux:label>

    <flux:progress value="75" />
</flux:field>

<flux:field>
    <flux:label>Storage</flux:label>

    <div class="flex items-center gap-4 -mt-2">
        <flux:progress value="75" />

        <span class="text-sm tabular-nums ...">75%</span>
    </div>
</flux:field>
```

## Controlled

```html
<flux:slider wire:model="progress" />

<flux:progress wire:model="progress" />
```

## Reference

### `flux:progress`

| Prop | Description |
| --- | --- |
| value | Current progress value (0 to max). Default: 0. |
| max | Maximum value. Default: 100. |
| color | Bar fill color (e.g., blue, red, green). Default: accent color. |

### CSS variables

| CSS Variable | Description |
| --- | --- |
| --flux-progress | The computed progress as a raw number (0–100). |
| --flux-progress-percentage | The computed progress as a percentage string (e.g., 75%). Used internally to set the bar width. |
