---
name: fluxui-development
description: "Use this skill for Flux UI development in Livewire applications only. Trigger when working with <flux:*> components, building or customizing Livewire component UIs, creating forms, modals, tables, or other interactive elements. Covers: flux: components (buttons, inputs, modals, forms, tables, date-pickers, kanban, badges, tooltips, etc.), component composition, Tailwind CSS styling, Heroicons/Lucide icon integration, validation patterns, responsive design, and theming. Do not use for non-Livewire frameworks or non-component styling."
license: MIT
metadata:
  author: laravel
---

# Flux UI Development

## Local Code Samples (Primary Reference)

This project includes comprehensive Flux UI code samples at `code-samples/flux-ui/`. **Always read the relevant sample file before using a component.** These samples contain props, variants, slots, events, CSS references, and real-world usage patterns.

### How to use

When working with a Flux component, read its sample file first:
- **Component**: `code-samples/flux-ui/components/{component-name}.md` (e.g., `button.md`, `modal.md`, `table.md`, `select.md`)
- **Layout**: `code-samples/flux-ui/layouts/{layout-name}.md` (e.g., `sidebar.md`, `header.md`)
- **Guide**: `code-samples/flux-ui/guides/{topic}.md` (e.g., `patterns.md`, `theming.md`, `customization.md`, `dark-mode.md`)

### Available Components (Pro Edition)

accordion, autocomplete, avatar, badge, brand, breadcrumbs, button, calendar, callout, card, carousel, chart, checkbox, color-picker, command, composer, context, date-picker, dropdown, editor, field, file-upload, heading, icon, input, kanban, modal, navbar, otp-input, pagination, pillbox, popover, profile, progress, radio, select, separator, skeleton, slider, switch, table, tabs, text, textarea, time-picker, timeline, toast, tooltip

### Available Layouts

header, sidebar

### Available Guides

customization, dark-mode, patterns, theming

## Additional Documentation

Use `search-docs` for Flux UI patterns not covered by the local code samples.

## Basic Usage

This project uses the Pro version of Flux UI, which includes all free and Pro components and variants.

Flux UI is a component library for Livewire built with Tailwind CSS. Use Flux UI components when available. Fall back to standard Blade components when no Flux component exists for your needs.

## Icons

Flux includes [Heroicons](https://heroicons.com/) as its default icon set. Search for exact icon names on the Heroicons site - do not guess or invent icon names.

```blade
<flux:button icon="arrow-down-tray">Export</flux:button>
```

For icons not available in Heroicons, use [Lucide](https://lucide.dev/). Import with:

```bash
php artisan flux:icon crown grip-vertical github
```

## Key Patterns (Quick Reference)

### Shorthand props

```blade
<!-- Shorthand (preferred for simple cases) -->
<flux:input type="email" wire:model="email" label="Email" />

<!-- Longform (when customization is needed) -->
<flux:field>
    <flux:label>Email</flux:label>
    <flux:input type="email" wire:model="email" />
    <flux:error name="email" />
</flux:field>
```

### Modals — named triggers vs wire:model

```blade
<!-- Named trigger approach -->
<flux:modal.trigger name="edit-profile">
    <flux:button>Edit</flux:button>
</flux:modal.trigger>
<flux:modal name="edit-profile" class="md:w-96">...</flux:modal>

<!-- Livewire data-binding approach (use .self modifier) -->
<flux:modal wire:model.self="showModal">...</flux:modal>

<!-- Programmatic control from Livewire -->
$this->modal('confirm')->show();
$this->modal('confirm')->close();
Flux::modals()->close();
```

### Tables with pagination

```blade
<flux:table :paginate="$this->items">
    <flux:table.columns>
        <flux:table.column>Name</flux:table.column>
        <flux:table.column sortable :sorted="$sortBy === 'date'" :direction="$sortDirection" wire:click="sort('date')">Date</flux:table.column>
    </flux:table.columns>
    <flux:table.rows>
        @foreach ($this->items as $item)
            <flux:table.row :key="$item->id">
                <flux:table.cell variant="strong">{{ $item->name }}</flux:table.cell>
                <flux:table.cell>{{ $item->date }}</flux:table.cell>
            </flux:table.row>
        @endforeach
    </flux:table.rows>
</flux:table>
```

## Verification

1. Check component renders correctly
2. Test interactive states
3. Verify mobile responsiveness

## Common Pitfalls

- Not reading the local code sample before using a component — always check `code-samples/flux-ui/components/` first
- Not checking if a Flux component exists before creating custom implementations
- Using `wire:model` without `.self` modifier on modals (nested elements can dispatch interfering input events)
- Forgetting that class conflicts with Flux internals need Tailwind's `!` modifier (e.g., `bg-zinc-800!`)
- Not following existing project patterns for Flux usage
