# Skeleton

Source: https://fluxui.dev/components/skeleton

Create placeholder content while loading data.

## Basic Example

```html
<flux:skeleton />
```

## Line of text

```html
<flux:skeleton.group animate="shimmer">
    <flux:skeleton.line class="mb-2 w-1/4" />
    <flux:skeleton.line />
    <flux:skeleton.line />
    <flux:skeleton.line class="w-3/4" />
</flux:skeleton.group>
```

## Animation

```html
<flux:skeleton />
<flux:skeleton animate="shimmer" />
<flux:skeleton animate="pulse" />
```

## Examples

Here are some examples of different ways you can use the skeleton component.

### Table

```html
<flux:skeleton.group animate="shimmer">
    <flux:table>
        <flux:table.columns>
            <flux:table.column>Customer</flux:table.column>
            <flux:table.column>Date</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column>Amount</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach (range(1, 5) as $order)
                <flux:table.row>
                    <flux:table.cell>
                        <div class="flex items-center gap-2">
                            <flux:skeleton class="rounded-full size-5" />

                            <div class="flex-1">
                                <flux:skeleton.line style="width: {{ rand(50, 100) }}%" />
                            </div>
                        </div>
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:skeleton.line />
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:skeleton.line />
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:skeleton.line />
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</flux:skeleton.group>
```

### Chart

Use the skeleton component to create a loading state for a chart.

```html
<flux:card class="dark:bg-zinc-800">
    <div class="flex flex-col gap-6">
        <div class="flex gap-12">
            <div>
                <flux:text>Today</flux:text>
                <flux:heading size="xl" class="mt-2 tabular-nums">$---</flux:heading>
                <flux:text class="mt-2 tabular-nums">-:-- PM</flux:text>
            </div>

            <div>
                <flux:text>Yesterday</flux:text>
                <flux:heading size="lg" class="mt-2 tabular-nums">$---</flux:heading>
            </div>
        </div>

        <flux:skeleton animate="shimmer" class="aspect-[4/1] size-full rounded-lg" />
    </div>
</flux:card>
```

## Reference

### `flux:skeleton`

| Prop | Description |
| --- | --- |
| `animate` | Options: `shimmer`, `pulse`. Default: no animation. |

| Slot | Description |
| --- | --- |
| default | Custom content for the skeleton. |

### `flux:skeleton.line`

| Prop | Description |
| --- | --- |
| `size` | Options: `base`, `lg`. Default: `base`. |
| `animate` | Options: `shimmer`, `pulse`. Default: none. Can be inherited from `flux:skeleton.group`. |

| Slot | Description |
| --- | --- |
| default | Custom content for the skeleton line. |

### `flux:skeleton.group`

| Prop | Description |
| --- | --- |
| `animate` | Sets animation for all skeletons in group. Options: `shimmer`, `pulse`. Default: none. |

| Slot | Description |
| --- | --- |
| default | The skeleton components to display in the group. |
