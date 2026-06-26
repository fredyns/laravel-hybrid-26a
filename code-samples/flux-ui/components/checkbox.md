# Checkbox

Source: https://fluxui.dev/components/checkbox

Select one or multiple options from a set.

## Basic Example

```html
<flux:field variant="inline">
    <flux:checkbox wire:model="terms" />
    <flux:label>I agree to the terms and conditions</flux:label>
    <flux:error name="terms" />
</flux:field>
```

## Checkbox group

```html
<flux:checkbox.group wire:model="notifications" label="Notifications">
    <flux:checkbox label="Push notifications" value="push" checked />
    <flux:checkbox label="Email" value="email" checked />
    <flux:checkbox label="In-app alerts" value="app" />
    <flux:checkbox label="SMS" value="sms" />
</flux:checkbox.group>
```

## With descriptions

```html
<flux:checkbox.group wire:model="subscription" label="Subscription preferences">
    <flux:checkbox checked value="newsletter" label="Newsletter" description="Receive our monthly newsletter with the latest updates and offers." />
    <flux:checkbox value="updates" label="Product updates" description="Stay informed about new features and product updates." />
    <flux:checkbox value="invitations" label="Event invitations" description="Get invitations to our exclusive events and webinars." />
</flux:checkbox.group>
```

## Horizontal fieldset

```html
<flux:fieldset>
    <flux:legend>Languages</flux:legend>
    <flux:description>Choose the languages you want to support.</flux:description>
    <div class="flex gap-4 *:gap-x-2">
        <flux:checkbox checked value="english" label="English" />
        <flux:checkbox checked value="spanish" label="Spanish" />
        <flux:checkbox value="french" label="French" />
        <flux:checkbox value="german" label="German" />
    </div>
</flux:fieldset>
```

## Check-all

```html
<flux:checkbox.group>
    <flux:checkbox.all />
    <flux:checkbox checked />
    <flux:checkbox />
    <flux:checkbox />
</flux:checkbox.group>
```

## Checked

```html
<flux:checkbox checked />
```

## Disabled

```html
<flux:checkbox disabled />
```

## Checkbox cards

```html
<flux:checkbox.group wire:model="subscription" label="Subscription preferences" variant="cards" class="max-sm:flex-col">
    <flux:checkbox checked value="newsletter" label="Newsletter" description="Get the latest updates and offers." />
    <flux:checkbox value="updates" label="Product updates" description="Learn about new features and products." />
    <flux:checkbox value="invitations" label="Event invitations" description="Invitatations to exclusive events." />
</flux:checkbox.group>
```

```html
<flux:checkbox.group ... class="max-sm:flex-col">
    <!-- ... -->
</flux:checkbox.group>
```

## Vertical cards

```html
<flux:checkbox.group label="Subscription preferences" variant="cards" class="flex-col">
    <flux:checkbox checked value="newsletter" label="Newsletter" description="Get the latest updates and offers." />
    <flux:checkbox value="updates" label="Product updates" description="Learn about new features and products." />
    <flux:checkbox value="invitations" label="Event invitations" description="Invitatations to exclusive events." />
</flux:checkbox.group>
```

## Cards with icons

```html
<flux:checkbox.group label="Subscription preferences" variant="cards" class="flex-col">
    <flux:checkbox checked value="newsletter" icon="newspaper" label="Newsletter" description="Get the latest updates and offers." />
    <flux:checkbox value="updates" icon="cube" label="Product updates" description="Learn about new features and products." />
    <flux:checkbox value="invitations" icon="calendar" label="Event invitations" description="Invitatations to exclusive events." />
</flux:checkbox.group>
```

## Custom card content

```html
<flux:checkbox.group label="Subscription preferences" variant="cards" class="flex-col">
    <flux:checkbox checked value="newsletter">
        <flux:checkbox.indicator />
        <div class="flex-1">
            <flux:heading class="leading-4">Newsletter</flux:heading>
            <flux:text size="sm" class="mt-2">Get the latest updates and offers.</flux:text>
        </div>
    </flux:checkbox>
    <flux:checkbox value="updates">
        <flux:checkbox.indicator />
        <div class="flex-1">
            <flux:heading class="leading-4">Product updates</flux:heading>
            <flux:text size="sm" class="mt-2">Learn about new features and products.</flux:text>
        </div>
    </flux:checkbox>
    <flux:checkbox value="invitations">
        <flux:checkbox.indicator />
        <div class="flex-1">
            <flux:heading class="leading-4">Event invitations</flux:heading>
            <flux:text size="sm" class="mt-2">Invitatations to exclusive events.</flux:text>
        </div>
    </flux:checkbox>
</flux:checkbox.group>
```

## Pills

```html
<flux:checkbox.group wire:model="categories" label="Categories" variant="pills">
    <flux:checkbox value="fantasy" label="Fantasy" />
    <flux:checkbox value="science-fiction" label="Science fiction" />
    <flux:checkbox value="horror" label="Horror" />
    <flux:checkbox value="mystery" label="Mystery" />
    <flux:checkbox value="romance" label="Romance" />
    <flux:checkbox value="autobiography" label="Autobiography" />
    <flux:checkbox value="thriller" label="Thriller" />
    <flux:checkbox value="poetry" label="Poetry" />
    <flux:checkbox value="children" label="Children" />
</flux:checkbox.group>
```

## Buttons

```html
<flux:checkbox.group wire:model="features" label="Features" variant="buttons">
    <flux:checkbox value="notifications" icon="bell" label="Notifications" />
    <flux:checkbox value="analytics" icon="chart-bar" label="Analytics" />
    <flux:checkbox value="backups" icon="cloud-arrow-up" label="Backups" />
</flux:checkbox.group>
```

## Reference

### `flux:checkbox`

| Prop | Description |
| --- | --- |
| wire:model | Binds the checkbox to a Livewire property. See the wire:model documentation for more information. |
| label | Label text displayed next to the checkbox. When provided, wraps the checkbox in a structure with an adjacent label. |
| description | Help text displayed below the checkbox. When provided alongside label, appears between the label and checkbox. |
| value | Value associated with the checkbox when used in a group. When the checkbox is checked, this value will be included in the array returned by the group's wire:model. |
| checked | Sets the checkbox to be checked by default. |
| indeterminate | Sets the checkbox to an indeterminate state, represented by a dash instead of a checkmark. Useful for "select all" checkboxes when only some items are selected. |
| disabled | Prevents user interaction with the checkbox. |
| invalid | Applies error styling to the checkbox. |

| Attribute | Description |
| --- | --- |
| data-flux-checkbox | Applied to the root element for styling and identification. |
| data-checked | Applied when the checkbox is checked. |
| data-indeterminate | Applied when the checkbox is in an indeterminate state. |

### `flux:checkbox.group`

| Prop | Description |
| --- | --- |
| wire:model | Binds the checkbox group to a Livewire property. The value will be an array of the selected checkboxes' values. See the wire:model documentation for more information. |
| label | Label text displayed above the checkbox group. When provided, wraps the group in a flux:field component with an adjacent flux:label component. |
| description | Help text displayed below the group label. When provided alongside label, appears between the label and the checkboxes. |
| variant | Visual style of the group. Options: default, cards (Pro), pills (Pro), buttons (Pro). |
| disabled | Prevents user interaction with all checkboxes in the group. |
| invalid | Applies error styling to all checkboxes in the group. |

| Slot | Description |
| --- | --- |
| default | The checkboxes to be grouped together. Can include `flux:checkbox`, `flux:checkbox.all`, and other elements. |

### `flux:checkbox.all`

| Prop | Description |
| --- | --- |
| label | Text label displayed next to the checkbox. |
| description | Help text displayed below the checkbox. |
| disabled | Prevents user interaction with the checkbox. |
