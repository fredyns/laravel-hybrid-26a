---
name: fluxui-development
description: "Use for any task involving Flux UI components in a Livewire application. Activate when the user asks to build, create, or edit any UI — forms, tables, modals, navigation, cards, buttons, inputs, layouts, or any Blade template using <flux:*> components. Also activate when the user mentions component names (flux:button, flux:input, flux:modal, flux:table, flux:navbar, etc.), asks about props or variants, or wants to know how a Flux component works. Use proactively whenever UI work is involved in a Livewire project — do not wait for the user to explicitly say 'use Flux'. Do not use for non-Livewire frameworks or pure Alpine.js-only UIs."
license: MIT
metadata:
  author: laravel
---

# Flux UI Development

## Documentation

Use `search-docs` for Flux-specific patterns not covered here. For component APIs, **read the local docs first** (see Lookup Workflow below) — they are faster and already version-matched.

## Lookup Workflow

Before writing any Flux component code:

1. Identify which component(s) the task needs from the index below.
2. Read `code-samples/flux-ui/components/<name>.md` for the exact API, variants, and examples.
3. For layout work, read `code-samples/flux-ui/layouts/header.md` or `layouts/sidebar.md`.
4. For composition patterns, read `code-samples/flux-ui/guides/patterns.md`.

## Component Index

### Free Components

| Component   | Doc file                      | Notes                            |
|-------------|-------------------------------|----------------------------------|
| Avatar      | components/avatar.md          |                                  |
| Badge       | components/badge.md           |                                  |
| Brand       | components/brand.md           |                                  |
| Breadcrumbs | components/breadcrumbs.md     |                                  |
| Button      | components/button.md          | Most used — read this first      |
| Callout     | components/callout.md         |                                  |
| Card        | components/card.md            |                                  |
| Checkbox    | components/checkbox.md        |                                  |
| Dropdown    | components/dropdown.md        |                                  |
| Field       | components/field.md           | Wraps inputs with label + error  |
| Heading     | components/heading.md         |                                  |
| Icon        | components/icon.md            |                                  |
| Input       | components/input.md           |                                  |
| Modal       | components/modal.md           |                                  |
| Navbar      | components/navbar.md          |                                  |
| OTP Input   | components/otp-input.md       |                                  |
| Pagination  | components/pagination.md      |                                  |
| Profile     | components/profile.md         |                                  |
| Progress    | components/progress.md        |                                  |
| Radio       | components/radio.md           |                                  |
| Select      | components/select.md          |                                  |
| Separator   | components/separator.md       |                                  |
| Skeleton    | components/skeleton.md        |                                  |
| Switch      | components/switch.md          |                                  |
| Table       | components/table.md           |                                  |
| Text        | components/text.md            |                                  |
| Textarea    | components/textarea.md        |                                  |
| Tooltip     | components/tooltip.md         |                                  |

### PRO-Only Components

These require a Flux Pro license (`livewire/flux-pro`). This project uses the **free edition** — do not use these unless the user explicitly confirms Pro is available.

| Component    | Doc file                       |
|--------------|--------------------------------|
| Accordion    | components/accordion.md        |
| Autocomplete | components/autocomplete.md     |
| Calendar     | components/calendar.md         |
| Carousel     | components/carousel.md         |
| Chart        | components/chart.md            |
| Color picker | components/color-picker.md     |
| Command      | components/command.md          |
| Composer     | components/composer.md         |
| Context menu | components/context.md          |
| Date picker  | components/date-picker.md      |
| Editor       | components/editor.md           |
| File upload  | components/file-upload.md      |
| Kanban       | components/kanban.md           |
| Pillbox      | components/pillbox.md          |
| Popover      | components/popover.md          |
| Slider       | components/slider.md           |
| Tabs         | components/tabs.md             |
| Time picker  | components/time-picker.md      |
| Timeline     | components/timeline.md         |
| Toast        | components/toast.md            |

### Layouts

| Layout  | Doc file           |
|---------|--------------------|
| Header  | layouts/header.md  |
| Sidebar | layouts/sidebar.md |

## Key Conventions

**Naming**: All components use the `flux:` prefix. Sub-components use dot notation: `flux:modal`, `flux:modal.trigger`, `flux:modal.close`.

**Wire binding**: Use `wire:model` on form inputs to bind to Livewire properties. Most input components accept `label`, `description`, and `:invalid` directly — no need to manually wrap with `flux:field` unless you need a custom layout.

```blade
<flux:input wire:model="email" label="Email" type="email" :invalid="$errors->has('email')" />
```

**Validation errors**: The `:invalid` prop applies error ring styling to the input itself. Always pair it with `flux:error` (or the built-in error handling via the `label` shorthand) so both the styling and message are present.

**Variants**: Many components accept a `variant` prop. Check the Reference table in the component doc — don't guess available values.

**Icons**: Flux uses [Heroicons](https://heroicons.com/) by default. Search the Heroicons site for exact names — do not invent icon names. For icons not in Heroicons, import from [Lucide](https://lucide.dev/) with `php artisan flux:icon <name>`.

```blade
<flux:button icon="arrow-down-tray">Export</flux:button>
```

**Tailwind**: Apply layout and spacing via the `class` prop. Some components expose inner element styling through scoped props like `track:class` or `input:class` — see the component doc.

**Dark mode**: Handled automatically by `@fluxAppearance`. Use `dark:` Tailwind variants only for custom elements alongside Flux components.

**Alpine**: Use Alpine for lightweight client-side interactions. Flux exposes a `$flux` magic object for controlling modals, popovers, etc.:

```blade
<button x-on:click="$flux.modal('confirm').show()">Open</button>
```

**Modal names in loops**: Always scope modal names per row to avoid one trigger opening all modals on the page:

```blade
<flux:modal.trigger :name="'delete-user-'.$user->id">...</flux:modal.trigger>
<flux:modal :name="'delete-user-'.$user->id">...</flux:modal>
```

## Common Patterns

### Form with validation

```blade
<form wire:submit="save">
    <div class="space-y-4">
        <flux:input wire:model="name" label="Name" :invalid="$errors->has('name')" />
        <flux:input wire:model="email" label="Email" type="email" :invalid="$errors->has('email')" />
        <flux:field>
            <flux:label badge="Optional">Phone</flux:label>
            <flux:input wire:model="phone" type="tel" :invalid="$errors->has('phone')" />
            <flux:error name="phone" />
        </flux:field>
        <flux:textarea wire:model="bio" label="Bio" rows="4" />
        <flux:button type="submit" variant="primary">Save</flux:button>
    </div>
</form>
```

### Confirmation modal

```blade
<flux:modal.trigger name="confirm-delete">
    <flux:button variant="danger">Delete</flux:button>
</flux:modal.trigger>

<flux:modal name="confirm-delete" class="min-w-[22rem]">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Are you sure?</flux:heading>
            <flux:text class="mt-2">This action cannot be undone.</flux:text>
        </div>
        <div class="flex gap-2">
            <flux:spacer />
            <flux:modal.close>
                <flux:button variant="ghost">Cancel</flux:button>
            </flux:modal.close>
            <flux:button wire:click="delete" variant="danger">Delete</flux:button>
        </div>
    </div>
</flux:modal>
```

### Data table

```blade
<flux:table>
    <flux:table.columns>
        <flux:table.column>Name</flux:table.column>
        <flux:table.column>Email</flux:table.column>
        <flux:table.column>Status</flux:table.column>
    </flux:table.columns>
    <flux:table.rows>
        @foreach ($users as $user)
            <flux:table.row :key="$user->id">
                <flux:table.cell variant="strong">{{ $user->name }}</flux:table.cell>
                <flux:table.cell>{{ $user->email }}</flux:table.cell>
                <flux:table.cell><flux:badge>{{ $user->status }}</flux:badge></flux:table.cell>
            </flux:table.row>
        @endforeach
    </flux:table.rows>
</flux:table>
```

## Verification

1. Confirm all sub-component names are correct (`flux:table.column`, not `flux:column`).
2. Confirm `:invalid` prop is set on inputs when validation errors should style the field.
3. Confirm modal names are unique per row in loops.
4. Test interactive states (hover, focus, disabled) and mobile responsiveness.

## Common Pitfalls

- Wrong sub-component names: `flux:column` / `flux:row` are NOT valid — use `flux:table.column`, `flux:table.row`, etc.
- Missing `:invalid` prop: `flux:error` shows the message text but won't apply error ring styling to the input without `:invalid`.
- Shared modal name in a loop: one trigger opens every modal on the page simultaneously.
- Using PRO components (`flux:accordion`, `flux:tabs`, `flux:date-picker`, etc.) without confirming a Pro license.
- Inventing icon names — always verify on the Heroicons site.
- Adding `@fluxAppearance` or `@fluxScripts` more than once in a layout.
