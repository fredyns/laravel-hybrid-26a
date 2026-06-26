# Time picker - PRO

Source: https://fluxui.dev/components/time-picker

Allow users to select specific times for scheduling events or setting appointments. Perfect for time-based filtering and precise scheduling.

## Basic usage

```html
<flux:time-picker value="11:30" />
```

```html
<flux:time-picker wire:model="time" />
```

## Input trigger

```html
<flux:time-picker type="input" />
```

## Without dropdown

```html
<flux:time-picker type="input" :dropdown="false" />
```

## Multiple times

```html
<flux:time-picker multiple />
```

## Time format

```html
<flux:time-picker time-format="12-hour" />
<flux:time-picker time-format="24-hour" />
```

## Interval

You can set the interval between the displayed time options by setting the interval attribute to a number of minutes. The default is 30 minutes.

```html
<flux:time-picker interval="60" />
```

## Min/max times

```html
<flux:time-picker min="09:00" max="17:00" />
```

```html
<!-- Prevent selection before now... -->
<flux:time-picker min="now" />

<!-- Prevent selection after now... -->
<flux:time-picker max="now" />
```

## Unavailable times

```html
<flux:time-picker unavailable="03:00,04:00,05:30-07:29" />
```

## Open to

Set the time that the time picker will open to. Otherwise, the time picker defaults to the selected time, or the current time.

```html
<flux:time-picker open-to="10:00" />
```

## Localization

```html
<flux:time-picker locale="ja-JP" />
```

## Reference

### `flux:time-picker`

| Prop | Description |
| --- | --- |
| wire:model | Binds the time picker to a Livewire property. See the wire:model documentation for more information. |
| value | Selected time(s). Format depends on mode: single time (H:i) or multiple times (H:i,H:i). |
| type | Type of time picker. Options: input, button. Default: button. |
| multiple | Allow users to select multiple times. Default: false. |
| time-format | Time format. Options: auto (default), 12-hour, 24-hour. |
| interval | Interval in minutes between the displayed time options. Default: 30. |
| min | Earliest selectable time. Can be a time string or "now". |
| max | Latest selectable time. Can be a time string or "now". |
| unavailable | Unavailable times. Can be a time string or a comma-separated list of time strings. |
| open-to | Time to open the time picker to. Default: selected time, or now. |
| label | Label text displayed above the time picker. When provided, wraps the component in a flux:field with an adjacent flux:label. |
| description | Help text displayed above the time picker. When provided alongside label, appears between the label and time picker within the flux:field wrapper. |
| description:trailing | The description provided will be displayed below the time picker instead of above it. |
| badge | Badge text displayed at the end of the flux:label component when the label prop is provided. |
| placeholder | Placeholder text displayed when no time is selected. |
| size | Size of the time picker. Options: sm, xs. |
| clearable | Displays a clear button when a time is selected. |
| disabled | Prevents user interaction with the time picker. |
| invalid | Applies error styling to the time picker. |
| locale | Set the locale for the time picker. Examples: fr, en-US, ja-JP. |

| Slot | Description |
| --- | --- |
| default | Custom content for the time picker. |

| Attribute | Description |
| --- | --- |
| data-flux-time-picker | Applied to the root element for styling and identification. |
