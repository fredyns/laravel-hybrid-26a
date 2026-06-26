# Kanban - PRO

Source: https://fluxui.dev/components/kanban

A collection of cards arranged in columns, representing different stages of a workflow.

## Basic Example

```html
<flux:kanban>
    @foreach ($this->columns as $column)
        <flux:kanban.column>
            <flux:kanban.column.header :heading="$column->title" :count="count($column->cards)" />

            <flux:kanban.column.cards>
                @foreach ($column->cards as $card)
                    <flux:kanban.card :heading="$card->title" />
                @endforeach
            </flux:kanban.column.cards>
        </flux:kanban.column>
    @endforeach
</flux:kanban>
```

## Column actions

```html
<flux:kanban.column>
    <flux:kanban.column.header :heading="$column->title" :count="count($column->cards)">
        <x-slot name="actions">
            <flux:dropdown>
                <flux:button variant="subtle" icon="ellipsis-horizontal" size="sm" />
                <flux:menu>
                    <flux:menu.item icon="plus">New card</flux:menu.item>
                    <flux:menu.item icon="archive-box">Archive column</flux:menu.item>
                    <flux:menu.separator />
                    <flux:menu.item variant="danger" icon="trash">Delete</flux:menu.item>
                </flux:menu>
            </flux:dropdown>
            <flux:button variant="subtle" icon="plus" size="sm" />
        </x-slot>
    </flux:kanban.column.header>
    <flux:kanban.column.cards>
        <!-- ... -->
    </flux:kanban.column.cards>
</flux:kanban.column>
```

## Column subheading

```html
<flux:kanban.column>
    <flux:kanban.column.header heading="Blacklog" subheading="Ideas and suggestions" />
    <flux:kanban.column.cards>
        <!-- ... -->
    </flux:kanban.column.cards>
</flux:kanban.column>
```

## Column footer

```html
<flux:kanban.column>
    <flux:kanban.column.header :heading="$column['title']" count="5" />
    <flux:kanban.column.cards>
        <!-- ... -->
    </flux:kanban.column.cards>
    <flux:kanban.column.footer>
        <form>
            <flux:kanban.card>
                <div class="flex items-center gap-1">
                    <flux:heading class="flex-1">
                        <input class="w-full outline-none" placeholder="New card...">
                    </flux:heading>
                    <flux:button type="submit" variant="filled" size="sm" inset="top bottom" class="-me-1.5">Add</flux:button>
                </div>
            </flux:kanban.card>
        </form>
        <flux:button variant="subtle" icon="plus" size="sm" align="start">
            New card
        </flux:button>
    </flux:kanban.column.footer>
</flux:kanban.column>
```

## Card as button

```html
<flux:kanban.card as="button" wire:click="edit" heading="Update privacy policy in app" />
```

## Card header

```html
<flux:kanban.card as="button" heading="Update privacy policy in app">
    <x-slot name="header">
        <div class="flex gap-2">
            <flux:badge color="blue" size="sm">UI</flux:badge>
            <flux:badge color="green" size="sm">Backend</flux:badge>
            <flux:badge color="red" size="sm">Bug</flux:badge>
        </div>
    </x-slot>
</flux:kanban.card>
```

## Card footer

```html
<flux:kanban.card as="button" heading="Update privacy policy in app">
    <x-slot name="footer">
        <flux:icon name="bars-3-bottom-left" variant="micro" class="text-zinc-400" />
        <flux:avatar.group>
            <flux:avatar circle size="xs" src="https://unavatar.io/x/calebporzio" />
            <flux:avatar circle size="xs" src="https://unavatar.io/github/hugosaintemarie" />
            <flux:avatar circle size="xs" src="https://unavatar.io/github/joshhanley" />
            <flux:avatar circle size="xs">3+</flux:avatar>
        </flux:avatar.group>
    </x-slot>
</flux:kanban.card>
```

## Reference

### `flux:kanban`

| Slot | Description |
| --- | --- |
| default | Place multiple flux:kanban.column components here to create columns in the kanban board. |

### `flux:kanban.column`

| Slot | Description |
| --- | --- |
| default | Should contain a flux:kanban.column.header, a flux:kanban.column.cards, and optionally a flux:kanban.column.footer component. |

### `flux:kanban.column.header`

| Prop | Description |
| --- | --- |
| heading | The title text to display in the column header. |
| subheading | Optional secondary text to display below the column header. |
| count | Optional number to display next to the heading, typically showing the number of cards in the column. |
| badge | Optional badge text or content to display next to the heading. Supports badge:* attributes for customization. |

| Slot | Description |
| --- | --- |
| default | Custom content to display in the header. Will override the heading and count props if provided. |
| actions | Optional slot for action buttons or dropdowns that appear on the right side of the column header. |

### `flux:kanban.column.cards`

| Slot | Description |
| --- | --- |
| default | Place multiple flux:kanban.card components here to populate the column with cards. |

### `flux:kanban.column.footer`

| Slot | Description |
| --- | --- |
| default | Custom content to display at the bottom of the column, commonly used for "Add card" buttons or forms. |

### `flux:kanban.card`

| Prop | Description |
| --- | --- |
| heading | The title text to display in the card. |
| as | Element to render the card as. Options: button, div (default). When set to button, the card becomes clickable. |
