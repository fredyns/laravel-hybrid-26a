# Timeline - PRO

Source: https://fluxui.dev/components/timeline

Display a series of events or steps in a vertical or horizontal timeline.

## Basic Example

```html
<flux:timeline>
    <flux:timeline.item>
        <flux:timeline.indicator>
            <flux:icon.eye variant="micro" />
        </flux:timeline.indicator>

        <flux:timeline.content>
            <flux:heading>
                curtisss
                <flux:text inline>requested a review from</flux:text>
                james_rob
                <flux:text inline>· 4 days ago</flux:text>
            </flux:heading>
        </flux:timeline.content>
    </flux:timeline.item>

    <flux:timeline.item>
        <flux:timeline.indicator>
            <flux:icon.tag variant="micro" />
        </flux:timeline.indicator>

        <flux:timeline.content>
            <flux:heading>
                curtisss
                <flux:text inline>added tag</flux:text>
                <flux:badge size="sm">feature</flux:badge>
            </flux:heading>
        </flux:timeline.content>
    </flux:timeline.item>

    <flux:timeline.item>
        <flux:timeline.indicator color="green">
            <flux:icon.check variant="micro" />
        </flux:timeline.indicator>

        <flux:timeline.content>
            <flux:heading>
                james_rob
                <flux:text inline>approved these changes · 3 days ago</flux:text>
            </flux:heading>
        </flux:timeline.content>
    </flux:timeline.item>
</flux:timeline>
```

## Large

```html
<flux:timeline size="lg">
    <flux:timeline.item>
        <flux:timeline.indicator>1</flux:timeline.indicator>

        <flux:timeline.content>
            <flux:heading>Submit</flux:heading>
            <flux:text>Complete the form and provide all necessary assets.</flux:text>
        </flux:timeline.content>
    </flux:timeline.item>

    <!-- ... -->
</flux:timeline>
```

## Horizontal

```html
<flux:timeline horizontal>
    <flux:timeline.item>
        <flux:timeline.indicator>
            <flux:icon.credit-card variant="micro" />
        </flux:timeline.indicator>

        <flux:timeline.content>
            <flux:heading>Order confirmed</flux:heading>
        </flux:timeline.content>
    </flux:timeline.item>

    <!-- ... -->
</flux:timeline>
```

## Status

```html
<flux:timeline horizontal>
    <flux:timeline.item status="complete">
        <flux:timeline.indicator>
            <flux:icon.credit-card variant="micro" />
        </flux:timeline.indicator>

        <flux:timeline.content>
            <flux:heading>Order confirmed</flux:heading>
        </flux:timeline.content>
    </flux:timeline.item>

    <flux:timeline.item status="current">
        <flux:timeline.indicator>
            <flux:icon.truck variant="micro" />
        </flux:timeline.indicator>

        <flux:timeline.content>
            <flux:heading>On its way</flux:heading>
        </flux:timeline.content>
    </flux:timeline.item>

    <flux:timeline.item status="incomplete">
        <flux:timeline.indicator>
            <flux:icon.home variant="micro" />
        </flux:timeline.indicator>

        <flux:timeline.content>
            <flux:heading>Delivered</flux:heading>
        </flux:timeline.content>
    </flux:timeline.item>
</flux:timeline>
```

## Indicator color

```html
<flux:timeline.indicator color="red">
    <flux:icon.x-mark variant="micro" />
</flux:timeline.indicator>

<flux:timeline.indicator color="amber">
    <flux:icon.exclamation-triangle variant="micro" />
</flux:timeline.indicator>

<flux:timeline.indicator color="green">
    <flux:icon.check variant="micro" />
</flux:timeline.indicator>
```

## Bare indicator

```html
<flux:timeline>
    <flux:timeline.item>
        <flux:timeline.indicator variant="bare">
            <flux:icon.document-text class="size-6 text-zinc-400" />
        </flux:timeline.indicator>

        <flux:timeline.content>
            <flux:heading>Draft created</flux:heading>
        </flux:timeline.content>
    </flux:timeline.item>

    <!-- ... -->
</flux:timeline>
```

## Block item

```html
<flux:timeline>
    <!-- ... -->

    <flux:timeline.item>
        <flux:timeline.block>
            <flux:callout variant="secondary">
                <flux:callout.heading>
                    james_rob
                    <flux:text>replied to your message · 4 days ago</flux:text>
                </flux:callout.heading>

                <x-slot name="actions">
                    <flux:button>View message -></flux:button>
                    <flux:button variant="ghost">Reply</flux:button>
                </x-slot>
            </flux:callout>
        </flux:timeline.block>
    </flux:timeline.item>

    <!-- ... -->
</flux:timeline>
```

## Block subgrid

```html
<flux:timeline>
    <!-- ... -->

    <flux:timeline.item>
        <flux:timeline.block class="bg-zinc-50 dark:bg-zinc-800 border dark:border-zinc-700 rounded-xl overflow-hidden">
            <flux:timeline.subgrid class="p-3 bg-zinc-50 dark:bg-zinc-800">
                <flux:avatar size="xs" circle src="https://github.com/joshhanley.png" />

                <div class="space-y-1">
                    <flux:heading>
                        james_rob
                        <flux:text class="font-medium" inline>commented · 4 days ago</flux:text>
                    </flux:heading>

                    <flux:text class="text-zinc-800 dark:text-white">
                        Contrast slider goes a bit wild at the top end...
                    </flux:text>

                    <div class="mt-2">
                        <div class="size-6 rounded-full border border-zinc-200 dark:border-zinc-600 hover:bg-white dark:hover:bg-zinc-700 text-zinc-300 dark:text-zinc-500 hover:text-zinc-400 dark:hover:text-zinc-300 shadow-xs grid place-items-center">
                            <flux:icon.face-smile variant="micro" />
                        </div>
                    </div>
                </div>
            </flux:timeline.subgrid>

            <div class="border-t border-zinc-200 dark:border-zinc-700"></div>

            <flux:timeline.subgrid class="p-3 bg-white dark:bg-zinc-900">
                <flux:avatar size="xs" circle src="https://github.com/calebporzio.png" />

                <div>
                    <flux:composer variant="input" placeholder="Leave a reply..." label="Prompt" label:sr-only description="Enter your prompt here" description:sr-only>
                        <x-slot name="actionsLeading">
                            <flux:button size="sm" variant="subtle" icon="paper-clip" />
                            <flux:button size="sm" variant="subtle" icon="slash" />
                            <flux:button size="sm" variant="subtle" icon="adjustments-horizontal" />
                        </x-slot>

                        <x-slot name="actionsTrailing">
                            <flux:button size="sm" variant="filled" icon="microphone" />
                            <flux:button type="submit" size="sm" variant="primary" icon="paper-airplane" />
                        </x-slot>
                    </flux:composer>
                </div>
            </flux:timeline.subgrid>
        </flux:timeline.block>
    </flux:timeline.item>

    <!-- ... -->
</flux:timeline>
```

## Alignment

```html
<flux:timeline align="start|baseline|center|end">
    <!-- ... -->
</flux:timeline>
```

## Baseline adjustment

```html
<flux:timeline.item class="[&_[data-flux-timeline-baseline]]:text-2xl" align="baseline">
    <flux:timeline.indicator>2</flux:timeline.indicator>

    <flux:timeline.content>
        <flux:heading size="xl">With baseline adjustment</flux:heading>
    </flux:timeline.content>
</flux:timeline.item>
```

## Spacing

```html
<flux:timeline class="[--flux-timeline-item-gap:3rem] [--flux-timeline-content-gap:1rem]">
    <!-- ... -->
</flux:timeline>
```

## Reference

### `flux:timeline`

| Prop | Description |
| --- | --- |
| horizontal | Render the timeline horizontally instead of vertically. |
| align | Cross-axis alignment of indicators to content. Options: start, baseline, center (default), end. |
| size | Size of the timeline indicators. Options: lg. |

| Slot | Description |
| --- | --- |
| default | The timeline items. |

| Class | Description |
| --- | --- |
| --flux-timeline-item-gap | CSS variable controlling the space between each timeline item. |
| --flux-timeline-content-gap | CSS variable controlling the space between the indicator and the content within an item. |

### `flux:timeline.item`

| Prop | Description |
| --- | --- |
| status | Controls indicator and connector line styling. Options: complete, current, incomplete. |
| align | Per-item alignment override. Options: start, baseline, center, end. |
| size | Per-item size override. Options: lg. |

| Slot | Description |
| --- | --- |
| default | The indicator and content components for this item. |

### `flux:timeline.indicator`

| Prop | Description |
| --- | --- |
| variant | Visual variant of the indicator. Options: bare (strips default sizing and background). |
| status | Override the parent item's status for indicator styling. Options: complete, current, incomplete. |
| color | Colored background for the indicator. Options: red, orange, amber, yellow, lime, green, emerald, teal, cyan, sky, blue, indigo, violet, purple, fuchsia, pink, rose. |

| Slot | Description |
| --- | --- |
| default | Content inside the indicator (icons, numbers, text). |

### `flux:timeline.content`

| Slot | Description |
| --- | --- |
| default | The content to display alongside the indicator. |

### `flux:timeline.block`

| Slot | Description |
| --- | --- |
| default | The block content to display. |

### `flux:timeline.subgrid`

| Slot | Description |
| --- | --- |
| default | The content to display within the subgrid layout. |
