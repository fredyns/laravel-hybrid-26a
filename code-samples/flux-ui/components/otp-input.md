# OTP Input

Source: https://fluxui.dev/components/otp-input

## Basic Example

```html
<flux:otp wire:model="code" length="6" />
```

## Example usage

```html
<flux:card>
    <form wire:submit="verify" class="space-y-8">
        <div class="max-w-64 mx-auto space-y-2">
            <flux:heading size="lg" class="text-center">Verify your account</flux:heading>
            <flux:text class="text-center">Please enter a one-time password from the authenticator app.</flux:text>
        </div>

        <flux:otp
            wire:model="code"
            length="6"
            label="OTP Code"
            label:sr-only
            :error:icon="false"
            error:class="text-center"
            class="mx-auto"
        />

        <div class="space-y-4">
            <flux:button variant="primary" type="submit" class="w-full">Verify</flux:button>
            <flux:button wire:click="resend" class="w-full">Resend code</flux:button>
        </div>
    </form>
</flux:card>
```

## Autosubmit

```html
<form wire:submit="verify" class="space-y-8">
    <div class="max-w-64 mx-auto space-y-2">
        <flux:heading size="lg" class="text-center">Verify your account</flux:heading>
        <flux:text class="text-center">Please enter a one-time password from the authenticator app.</flux:text>
    </div>

    <div class="space-y-6">
        <flux:otp wire:model="code" length="6" submit="auto" class="mx-auto" />
    </div>
</form>
```

## Alphanumeric

```html
<flux:otp
    wire:model="licenseKey"
    length="10"
    mode="alphanumeric"
    label="License key"
    description:trailing="Enter the license key printed on the installation disc"
/>
```

## Private

masking sensitive value

```html
<flux:otp wire:model="pin" length="4" private label="PIN Code" />
```

## Separator

```html
<flux:otp wire:model="code">
    <flux:otp.input />
    <flux:otp.input />
    <flux:otp.input />

    <flux:otp.separator />

    <flux:otp.input />
    <flux:otp.input />
    <flux:otp.input />
</flux:otp>
```

## Group

```html
<flux:otp wire:model="code">
    <flux:otp.group>
        <flux:otp.input />
        <flux:otp.input />
        <flux:otp.input />
        <flux:otp.input />
        <flux:otp.input />
        <flux:otp.input />
    </flux:otp.group>
</flux:otp>
```

## Group separator

```html
<flux:otp wire:model="code">
    <flux:otp.group>
        <flux:otp.input />
        <flux:otp.input />
        <flux:otp.input />
    </flux:otp.group>

    <flux:otp.separator />

    <flux:otp.group>
        <flux:otp.input />
        <flux:otp.input />
        <flux:otp.input />
    </flux:otp.group>
</flux:otp>
```

## Reference

### `flux:otp`

| Prop | Description |
| --- | --- |
| `wire:model` | Binds the value to a Livewire property. |
| `value` | Current value as a string. |
| `length` | Number of input fields. |
| `mode` | Options: `numeric` (default), `alphanumeric`, `alpha`. |
| `private` | Masks input values. |
| `submit` | Options: (default), `auto` (submit when all fields are filled). |
| `autocomplete` | Autocomplete attribute for first input. Default: `one-time-code`. |

| Slot | Description |
| --- | --- |
| `default` | Custom input fields and separators. When used, the `length` prop is ignored. |

### `flux:otp.input`

| Prop | Description |
| --- | --- |
| `â€”` | `â€”` |

### `flux:otp.separator`

| Prop | Description |
| --- | --- |
| `â€”` | `â€”` |

### `flux:otp.group`

| Slot | Description |
| --- | --- |
| `default` | The input fields to include in the group. |
