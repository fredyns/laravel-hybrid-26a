# Switch

Source: https://fluxui.dev/components/switch

## Basic Example

```html
<flux:switch wire:model="enabled" />
```

## Fieldset

```html
<flux:fieldset>
    <flux:legend>Email notifications</flux:legend>

    <div class="space-y-4">
        <flux:switch
            wire:model.live="communication"
            label="Communication emails"
            description="Receive emails about your account activity."
        />

        <flux:separator variant="subtle" />

        <flux:switch
            wire:model.live="marketing"
            label="Marketing emails"
            description="Receive emails about new products, features, and more."
        />

        <flux:separator variant="subtle" />

        <flux:switch
            wire:model.live="social"
            label="Social emails"
            description="Receive emails for friend requests, follows, and more."
        />

        <flux:separator variant="subtle" />

        <flux:switch
            wire:model.live="security"
            label="Security emails"
            description="Receive emails about your account activity and security."
        />
    </div>
</flux:fieldset>
```

## Left align

```html
<flux:fieldset>
    <flux:legend>Email notifications</flux:legend>

    <div class="space-y-3">
        <flux:switch label="Communication emails" align="left" />
        <flux:switch label="Marketing emails" align="left" />
        <flux:switch label="Social emails" align="left" />
        <flux:switch label="Security emails" align="left" />
    </div>
</flux:fieldset>
```

## Reference

### `flux:switch`

| Prop | Description |
| --- | --- |
| `wire:model` | Binds to a Livewire property. |
| `label` | Label text. When set, wraps in a `flux:field` with an adjacent `flux:label`. |
| `description` | Help text shown with `label` inside the `flux:field` wrapper. |
| `align` | Options: `right`\|`start` (default), `left`\|`end`. |
| `disabled` | Prevents user interaction. |

| Slot | Description |
| --- | --- |
| default | Custom content for the switch. |

| Attribute | Description |
| --- | --- |
| data-flux-switch | Applied to the root element for styling and identification. |
