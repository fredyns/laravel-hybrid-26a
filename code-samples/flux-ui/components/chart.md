# Chart - PRO

Source: https://fluxui.dev/components/chart

Flux's Chart component is a lightweight, zero-dependency tool for building charts in your Livewire applications. It is designed to be simple but extremely flexible, so that you can assemble and style your charts exactly as you need them.

>Currently, Flux supports line and area charts. Additional chart types are planned for future releases.

## Basic Example

```html
<flux:chart wire:model="data" class="aspect-3/1">
    <flux:chart.svg>
        <flux:chart.line field="visitors" class="text-pink-500 dark:text-pink-400" />

        <flux:chart.axis axis="x" field="date">
            <flux:chart.axis.line />
            <flux:chart.axis.tick />
        </flux:chart.axis>

        <flux:chart.axis axis="y">
            <flux:chart.axis.grid />
            <flux:chart.axis.tick />
        </flux:chart.axis>

        <flux:chart.cursor />
    </flux:chart.svg>

    <flux:chart.tooltip>
        <flux:chart.tooltip.heading field="date" :format="['year' => 'numeric', 'month' => 'numeric', 'day' => 'numeric']" />
        <flux:chart.tooltip.value field="visitors" label="Visitors" />
    </flux:chart.tooltip>
</flux:chart>
```

## Data structure

```html
<?php use Livewire\Component;

class Dashboard extends Component
{
    public array $data = [
        ['date' => '2026-01-27', 'visitors' => 267],
        ['date' => '2026-01-26', 'visitors' => 259],
        ['date' => '2026-01-25', 'visitors' => 269],
        // ...
    ];
}
```

```html
<flux:chart wire:model="data" />
```

```html
<flux:chart :value="$this->data" />
```

```html
<flux:chart :value="[1, 2, 3, 4, 5]" />
```

## Line chart

```html
<flux:chart wire:model="data" class="aspect-[3/1]">
    <flux:chart.svg>
        <flux:chart.line field="memory" class="text-pink-500" />
        <flux:chart.point field="memory" class="text-pink-400" />
        <flux:chart.axis axis="x" field="date">
            <flux:chart.axis.tick />
            <flux:chart.axis.line />
        </flux:chart.axis>
        <flux:chart.axis axis="y" tick-values="[0, 128, 256, 384, 512]" :format="['style' => 'unit', 'unit' => 'megabyte']">
            <flux:chart.axis.grid />
            <flux:chart.axis.tick />
        </flux:chart.axis>
    </flux:chart.svg>
</flux:chart>
```

## Area chart

```html
<flux:chart wire:model="data" class="aspect-3/1">
    <flux:chart.svg>
        <flux:chart.line field="stock" class="text-blue-500 dark:text-blue-400" curve="none" />
        <flux:chart.area field="stock" class="text-blue-200/50 dark:text-blue-400/30" curve="none" />
        <flux:chart.axis axis="y" position="right" tick-prefix="$" :format="[
            'notation' => 'compact',
            'compactDisplay' => 'short',
            'maximumFractionDigits' => 1,
        ]">
            <flux:chart.axis.grid />
            <flux:chart.axis.tick />
        </flux:chart.axis>
        <flux:chart.axis axis="x" field="date">
            <flux:chart.axis.tick />
            <flux:chart.axis.line />
        </flux:chart.axis>
    </flux:chart.svg>
</flux:chart>
```

## Multiple lines

```html
<flux:chart wire:model="data">
    <flux:chart.viewport class="min-h-[20rem]" >
        <flux:chart.svg>
            <flux:chart.line field="twitter" class="text-blue-500" curve="none" />
            <flux:chart.point field="twitter" class="text-blue-500" r="6" stroke-width="3" />
            <flux:chart.line field="facebook" class="text-red-500" curve="none" />
            <flux:chart.point field="facebook" class="text-red-500" r="6" stroke-width="3" />
            <flux:chart.line field="instagram" class="text-green-500" curve="none" />
            <flux:chart.point field="instagram" class="text-green-500" r="6" stroke-width="3" />
            <flux:chart.axis axis="x" field="date">
                <flux:chart.axis.tick />
                <flux:chart.axis.line />
            </flux:chart.axis>
            <flux:chart.axis axis="y" tick-start="0" tick-end="1" :format="[
                'style' => 'percent',
                'minimumFractionDigits' => 0,
                'maximumFractionDigits' => 0,
            ]">
                <flux:chart.axis.grid />
                <flux:chart.axis.tick />
            </flux:chart.axis>
        </flux:chart.svg>
    </flux:chart.viewport>
    <div class="flex justify-center gap-4 pt-4">
        <flux:chart.legend label="Instagram">
            <flux:chart.legend.indicator class="bg-green-400" />
        </flux:chart.legend>
        <flux:chart.legend label="Twitter">
            <flux:chart.legend.indicator class="bg-blue-400" />
        </flux:chart.legend>
        <flux:chart.legend label="Facebook">
            <flux:chart.legend.indicator class="bg-red-400" />
        </flux:chart.legend>
    </div>
</flux:chart>
```

## Live summary

```html
<flux:card>
    <flux:chart class="grid gap-6" wire:model="data">
        <flux:chart.summary class="flex gap-12">
            <div>
                <flux:text>Today</flux:text>
                <flux:heading size="xl" class="mt-2 tabular-nums">
                    <flux:chart.summary.value field="sales" :format="['style' => 'currency', 'currency' => 'USD']" />
                </flux:heading>
                <flux:text class="mt-2 tabular-nums">
                    <flux:chart.summary.value field="date" :format="['hour' => 'numeric', 'minute' => 'numeric', 'hour12' => true]" />
                </flux:text>
            </div>
            <div>
                <flux:text>Yesterday</flux:text>
                <flux:heading size="lg" class="mt-2 tabular-nums">
                    <flux:chart.summary.value field="yesterday" :format="['style' => 'currency', 'currency' => 'USD']" />
                </flux:heading>
            </div>
        </flux:chart.summary>
        <flux:chart.viewport class="aspect-[3/1]">
            <flux:chart.svg>
                <flux:chart.line field="yesterday" class="text-zinc-300 dark:text-white/40" stroke-dasharray="4 4" curve="none" />
                <flux:chart.line field="sales" class="text-sky-500 dark:text-sky-400" curve="none" />
                <flux:chart.axis axis="x" field="date">
                    <flux:chart.axis.grid />
                    <flux:chart.axis.tick />
                    <flux:chart.axis.line />
                </flux:chart.axis>
                <flux:chart.axis axis="y">
                    <flux:chart.axis.tick />
                </flux:chart.axis>
                <flux:chart.cursor />
            </flux:chart.svg>
        </flux:chart.viewport>
    </flux:chart>
</flux:card>
```

## Sparkline

```html
<flux:chart :value="[15, 18, 16, 19, 22, 25, 28, 25, 29, 28, 32, 35]" class="w-[5rem] aspect-[3/1]">
    <flux:chart.svg gutter="0">
        <flux:chart.line class="text-green-500 dark:text-green-400" />
    </flux:chart.svg>
</flux:chart>
```

## Dashboard stat

```html
<flux:card class="overflow-hidden min-w-[12rem]">
    <flux:text>Revenue</flux:text>
    <flux:heading size="xl" class="mt-2 tabular-nums">$12,345</flux:heading>
    <flux:chart class="-mx-8 -mb-8 h-[3rem]" :value="[10, 12, 11, 13, 15, 14, 16, 18, 17, 19, 21, 20]">
        <flux:chart.svg gutter="0">
            <flux:chart.line class="text-sky-200 dark:text-sky-400" />
            <flux:chart.area class="text-sky-100 dark:text-sky-400/30" />
        </flux:chart.svg>
    </flux:chart>
</flux:card>
```

## Chart padding

```html
<flux:chart>
    <flux:chart.svg gutter="12 0 12 8">
        <!-- ... -->
    </flux:chart.svg>
</flux:chart>
```

## Axis scale

```html
<flux:chart.axis axis="y" scale="linear">
    <!-- ... -->
</flux:chart.axis>
```

## Axis lines

```html
<flux:chart.svg>
    <!-- ... -->
    <flux:chart.axis axis="x">
        <!-- Horizontal "X" axis line: -->
        <flux:chart.axis.line />
    </flux:chart.axis>
    <flux:chart.axis axis="y">
        <!-- Vertical "Y" axis line: -->
        <flux:chart.axis.line />
    </flux:chart.axis>
</flux:chart.svg>
```

```html
<!-- A dark gray axis line that is 2px wide and has a gray color: -->
<flux:chart.axis.line class="text-zinc-800" stroke-width="2" />
```

## Zero line

```html
<flux:chart.svg>
    <!-- ... -->
    <!-- Zero line: -->
    <flux:chart.zero-line />
</flux:chart.svg>
```

```html
<!-- A dark gray zero line that is 2px wide and has a gray color: -->
<flux:chart.zero-line class="text-zinc-800" stroke-width="2" />
```

## Grid lines

```html
<flux:chart.svg>
    <!-- ... -->
    <flux:chart.axis axis="x">
        <!-- Vertical grid lines: -->
        <flux:chart.axis.grid />
    </flux:chart.axis>
    <flux:chart.axis axis="y">
        <!-- Horizontal grid lines: -->
        <flux:chart.axis.grid />
    </flux:chart.axis>
</flux:chart.svg>
```

```html
<!-- A dashed grid line that is 2px wide and has a gray color: -->
<flux:chart.axis.grid class="text-zinc-200/50" stroke-width="2" stroke-dasharray="4,4" />
```

## Ticks

```html
<flux:chart.svg>
    <!-- ... -->
    <flux:chart.axis axis="x">
        <!-- X axis tick mark lines: -->
        <flux:chart.axis.mark />
        <!-- X axis tick labels: -->
        <flux:chart.axis.tick />
    </flux:chart.axis>
    <flux:chart.axis axis="y">
        <!-- Y axis tick mark lines: -->
        <flux:chart.axis.mark />
        <!-- Y axis tick labels: -->
        <flux:chart.axis.tick />
    </flux:chart.axis>
</flux:chart.svg>
```

```html
<!-- A tick mark line that is 10px long, 2px wide, and has a gray color: -->
<flux:chart.axis.mark class="text-zinc-300" stroke-width="2" y1="0" y2="10" />
```

```html
<!-- A tick label that is 12px, has a blue color, is center aligned, and is 2.5rem from the axis line: -->
<flux:chart.axis.tick class="text-xs text-blue-500" text-anchor="middle" dy="2.5rem" />
```

## Tick frequency

```html
<flux:chart.axis axis="y" tick-count="5">
    <!-- ... -->
</flux:chart.axis>
```

```html
<flux:chart.axis axis="y" tick-start="min">
    <!-- ... -->
</flux:chart.axis>
```

```html
<flux:chart.axis axis="y" tick-end="max">
    <!-- ... -->
</flux:chart.axis>
```

```html
<flux:chart.axis axis="y" tick-values="[0, 128, 256, 384, 512]" tick-suffix="MB">
    <!-- ... -->
</flux:chart.axis>
```

## Tick formatting

```html
<flux:chart.svg>
    <!-- ... -->
    <!-- Format the X axis tick labels to display the month and day: -->
    <flux:chart.axis axis="x" :format="['month' => 'long', 'day' => 'numeric']">
        <!-- X axis tick labels: -->
        <flux:chart.axis.tick />
    </flux:chart.axis>
    <!-- Format the Y axis tick labels to display the value in USD: -->
    <flux:chart.axis axis="y" :format="['style' => 'currency', 'currency' => 'USD']">
        <!-- Y axis tick labels: -->
        <flux:chart.axis.tick />
    </flux:chart.axis>
</flux:chart.svg>
```

```html
<flux:chart.axis axis="y" tick-prefix="$">
    <!-- ... -->
</flux:chart.axis>
```

```html
<flux:chart.axis axis="y" tick-suffix="MB">
    <!-- ... -->
</flux:chart.axis>
```

## Cursor

```html
<flux:chart.svg>
    <!-- ... -->
    <flux:chart.cursor />
</flux:chart.svg>
```

```html
<!-- A dashed, black cursor that is 1px wide: -->
<flux:chart.cursor class="text-zinc-800" stroke-width="1" stroke-dasharray="4,4" />
```

## Tooltip

```html
<flux:chart>
    <flux:chart.svg>
        <!-- ... -->
    </flux:chart.svg>
    <flux:chart.tooltip>
        <flux:chart.tooltip.heading field="date" />
        <flux:chart.tooltip.value field="visitors" label="Visitors" />
        <flux:chart.tooltip.value field="views" label="Views" :format="['notation' => 'compact']" />
    </flux:chart.tooltip>
</flux:chart>
```

```html
<flux:chart.tooltip>
    <flux:chart.tooltip.heading field="date" />
    <flux:chart.tooltip.value field="visitors" label="Visitors" />
    <flux:chart.tooltip.value field="views" label="Page Views" :format="['notation' => 'compact']" />
</flux:chart.tooltip>
```

## Legend

```html
<flux:chart wire:model="data">
    <flux:chart.viewport class="aspect-3/1">
        <flux:chart.svg>
            <flux:chart.line class="text-blue-500" field="visitors" />
            <flux:chart.line class="text-red-500" field="views" />
        </flux:chart.svg>
    </flux:chart.viewport>
    <div class="flex justify-center gap-4 pt-4">
        <flux:chart.legend label="Visitors">
            <flux:chart.legend.indicator class="bg-blue-400" />
        </flux:chart.legend>
        <flux:chart.legend label="Views">
            <flux:chart.legend.indicator class="bg-red-400" />
        </flux:chart.legend>
    </div>
</flux:chart>
```

## Summary

```html
<flux:chart wire:model="data">
    <flux:chart.summary>
        <flux:chart.summary.value field="visitors" :format="['notation' => 'compact']" />
    </flux:chart.summary>
    <flux:chart.viewport class="aspect-[3/1]">
        <!-- ... -->
    </flux:chart.viewport>
</flux:chart>
```

```html
<flux:chart.summary.value field="visitors" fallback="1200" />
```

## Formatting numbers

```html
<flux:chart.axis axis="y" :format="['style' => 'currency', 'currency' => 'USD']" />
```

## Formatting dates

```html
<flux:chart.axis axis="x" field="date" :format="['month' => 'long', 'day' => 'numeric']" />
```

## Reference

### `flux:chart`

| Prop | Description |
| --- | --- |
| wire:model | Binds the chart to a Livewire property containing the data to display. See the wire:model documentation for more information. |
| value | Array of data points for the chart. Each point should be an associative array with named fields. Used when not binding with wire:model. |
| curve | Default line curve type for all lines in the chart. Options: smooth (default), none. |

| Slot | Description |
| --- | --- |
| default | Chart components including svg, viewport, summary, tooltip, and legend elements. |

| CSS | Description |
| --- | --- |
| class | Additional CSS classes applied to the chart container. Common use: `aspect-3/1` for aspect ratio control. |

| Attribute | Description |
| --- | --- |
| data-flux-chart | Applied to the root element for styling and identification. |

### `flux:chart.svg`

| Slot | Description |
| --- | --- |
| default | Chart visualization components like lines, areas, axes, and interactive elements. |

### `flux:chart.line`

| Prop | Description |
| --- | --- |
| field | Name of the data field to plot on the y-axis. Required. |
| curve | Line curve type. Options: smooth (default), none. |

| CSS | Description |
| --- | --- |
| class | Additional CSS classes applied to the line. Common use: `text-{color}` for line color. |

### `flux:chart.area`

| Prop | Description |
| --- | --- |
| field | Name of the data field to plot on the y-axis. Required. |
| curve | Area curve type. Options: smooth (default), none. |

| CSS | Description |
| --- | --- |
| class | Additional CSS classes applied to the area. Common use: `text-{color}` for area color. |

### `flux:chart.point`

| Prop | Description |
| --- | --- |
| field | Name of the data field to plot points for. Required. |

| CSS | Description |
| --- | --- |
| class | Additional CSS classes applied to the points. Common use: `text-{color}` for point color. |

### `flux:chart.axis`

| Prop | Description |
| --- | --- |
| axis | Axis to configure. Options: x, y. Required. |
| field | For x-axis, the data field to use for labels. |
| format | Date/number formatting options for axis labels. See Formatting for more details. |

### `flux:chart.axis.mark`

| Prop | Description |
| --- | --- |
| position | Position of the tick marks. Options: top, bottom, left, right. |

| CSS | Description |
| --- | --- |
| class | Additional CSS classes applied to the tick marks. |

### `flux:chart.axis.line`

| CSS | Description |
| --- | --- |
| class | Additional CSS classes applied to the axis line. Common use: text-{color} for line color, stroke-width="1" for line thickness. |

### `flux:chart.axis.grid`

| CSS | Description |
| --- | --- |
| class | Additional CSS classes applied to the gridlines. Common use: text-{color} for line color, stroke-width="{width}" for line thickness. |

### `flux:chart.axis.tick`

| Prop | Description |
| --- | --- |
| format | Date/number formatting options for tick labels. See Formatting for more details. |

| CSS | Description |
| --- | --- |
| class | Additional CSS classes applied to the tick labels. Common use: `text-xs` for font size, `text-{color}` for text color. |

### `flux:chart.zero-line`

| CSS | Description |
| --- | --- |
| class | Additional CSS classes applied to the zero line. Common use: text-{color} for line color, stroke-width="{width}" for line thickness. |

### `flux:chart.tooltip`

| Prop | Description |
| --- | --- |
| field | Data field to display in the tooltip. |
| format | Date/number formatting options for tooltip values. |

### Slot

| Slot | Description |
| --- | --- |
| default | Content to display in the tooltip. Can include heading and value components. |

### `flux:chart.tooltip.heading`

| Prop | Description |
| --- | --- |
| field | Data field to display in the tooltip heading. |
| format | Date/number formatting options for tooltip values. See Formatting for more details. |

### `flux:chart.tooltip.value`

| Prop | Description |
| --- | --- |
| field | Data field to display in the tooltip. |
| format | Date/number formatting options for tooltip values. See Formatting for more details. |
| label | Label text for the value. |

### `flux:chart.cursor`

| CSS | Description |
| --- | --- |
| class | Additional CSS classes applied to the cursor line. |

### `flux:chart.summary`

| Slot | Description |
| --- | --- |
| default | Content to display in the summary section. Can include flux:chart.summary.value components to display specific data values. |

### `flux:chart.summaryvalue`

| Prop | Description |
| --- | --- |
| field | Data field to display. |
| fallback | Fallback text to display if the value is not found or cannot be formatted. |
| format | Date/number formatting options. See Formatting for more details. |
| label | Label text for the value. |

### `flux:chart.legend`

| Prop | Description |
| --- | --- |
| field | Data field this legend item represents. |
| label | Label text for the legend item. |
| format | Date/number formatting options. See Formatting for more details. |

| Slot | Description |
| --- | --- |
| default | Content to display in the legend. Can include arbitrary content, including `flux:chart.legend.indicator` components. |
