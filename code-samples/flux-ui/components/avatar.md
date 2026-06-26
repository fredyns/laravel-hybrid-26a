# Avatar

Source: https://fluxui.dev/components/avatar

Display an image or initials as an avatar.

## Basic Example

```html
<flux:avatar src="https://unavatar.io/x/calebporzio" />
```

## Tooltip

```html
<flux:avatar tooltip="Caleb Porzio" src="https://unavatar.io/x/calebporzio" />

<!-- Or infer from the name prop... -->
<flux:avatar tooltip name="Caleb Porzio" src="https://unavatar.io/x/calebporzio" />
```

## Initials

```html
<flux:avatar name="Caleb Porzio" />
<flux:avatar name="calebporzio" />
<flux:avatar name="calebporzio" initials:single />

<!-- Or use the initials prop directly... -->
<flux:avatar initials="CP" />
```

## Size

```html
<!-- Extra large: size-16 (64px) -->
<flux:avatar size="xl" src="https://unavatar.io/x/calebporzio" />

<!-- Large: size-12 (48px) -->
<flux:avatar size="lg" src="https://unavatar.io/x/calebporzio" />

<!-- Default: size-10 (40px) -->
<flux:avatar src="https://unavatar.io/x/calebporzio" />

<!-- Small: size-8 (32px) -->
<flux:avatar size="sm" src="https://unavatar.io/x/calebporzio" />

<!-- Extra small: size-6 (24px) -->
<flux:avatar size="xs" src="https://unavatar.io/x/calebporzio" />
```

## Icon

```html
<flux:avatar icon="user" />
<flux:avatar icon="phone" />
<flux:avatar icon="computer-desktop" />
```

## Colors

```html
<flux:avatar name="Caleb Porzio" color="red" />
<flux:avatar name="Caleb Porzio" color="orange" />
<flux:avatar name="Caleb Porzio" color="amber" />
<flux:avatar name="Caleb Porzio" color="yellow" />
<flux:avatar name="Caleb Porzio" color="lime" />
<flux:avatar name="Caleb Porzio" color="green" />
<flux:avatar name="Caleb Porzio" color="emerald" />
<flux:avatar name="Caleb Porzio" color="teal" />
<flux:avatar name="Caleb Porzio" color="cyan" />
<flux:avatar name="Caleb Porzio" color="sky" />
<flux:avatar name="Caleb Porzio" color="blue" />
<flux:avatar name="Caleb Porzio" color="indigo" />
<flux:avatar name="Caleb Porzio" color="violet" />
<flux:avatar name="Caleb Porzio" color="purple" />
<flux:avatar name="Caleb Porzio" color="fuchsia" />
<flux:avatar name="Caleb Porzio" color="pink" />
<flux:avatar name="Caleb Porzio" color="rose" />
```

## Auto color

```html
<flux:avatar name="Caleb Porzio" color="auto" />

<!-- Use color:seed to generate a consistent color based -->
<!-- on something unchanging like a user's ID... -->
<flux:avatar name="Caleb Porzio" color="auto" color:seed="{{ $user->id }}" />
```

## Circle

```html
<flux:avatar circle src="https://unavatar.io/x/calebporzio" />
```

## Badge

```html
<flux:avatar badge badge:color="green" src="https://unavatar.io/x/calebporzio" />
<flux:avatar badge badge:color="zinc" badge:position="top right" badge:circle badge:variant="outline" src="https://unavatar.io/x/calebporzio" />
<flux:avatar badge="25" src="https://unavatar.io/x/calebporzio" />
<flux:avatar circle badge="👍" badge:circle src="https://unavatar.io/x/calebporzio" />
<flux:avatar circle src="https://unavatar.io/x/calebporzio">
    <x-slot:badge>
        <img class="size-3" src="https://unavatar.io/github/hugosaintemarie" />
    </x-slot:badge>
</flux:avatar>
```

## Groups

```html
<flux:avatar.group>
    <flux:avatar src="https://unavatar.io/x/calebporzio" />
    <flux:avatar src="https://unavatar.io/github/hugosaintemarie" />
    <flux:avatar src="https://unavatar.io/github/joshhanley" />
    <flux:avatar>3+</flux:avatar>
</flux:avatar.group>

<!-- Adapt rings to custom background... -->
<flux:avatar.group class="**:ring-zinc-100 dark:**:ring-zinc-800">
    <flux:avatar circle src="https://unavatar.io/x/calebporzio" />
    <flux:avatar circle src="https://unavatar.io/github/hugosaintemarie" />
    <flux:avatar circle src="https://unavatar.io/github/joshhanley" />
    <flux:avatar circle>3+</flux:avatar>
</flux:avatar.group>
```

## As button

```html
<flux:avatar as="button" src="https://unavatar.io/x/calebporzio" />
```

## As link

```html
<flux:avatar href="https://x.com/calebporzio" src="https://unavatar.io/x/calebporzio" />
```

## Examples

```html
<div>
    <div class="flex items-center gap-2">
        <flux:icon.star variant="solid" />
        <flux:icon.star variant="solid" />
        <flux:icon.star variant="solid" />
        <flux:icon.star variant="solid" />
        <flux:icon.star variant="solid" />
    </div>

    <flux:heading size="xl" class="mt-4 italic">
        <p>IMO Livewire takes Blade to the next level. It's basically what Blade should be by default. 🔥</p>
    </flux:heading>

    <div class="mt-6 flex items-center gap-4">
        <flux:avatar size="lg" src="https://unavatar.io/x/taylorotwell" />
        <div>
            <flux:heading size="lg">Taylor Otwell</flux:heading>
            <flux:text>Creator of Laravel</flux:text>
        </div>
    </div>
</div>
```

## Reference

### `flux:avatar`

| Prop | Description |
| --- | --- |
| name | User's name to display as initials. If provided without initials, this will be used to generate initials automatically. |
| src | URL to the image to display as avatar. |
| initials | Custom initials to display when no src is provided. Will override name if provided. |
| alt | Alternative text for the avatar image. (Default: name if provided) |
| size | Size of the avatar. Options: xs (24px), sm (32px), (default: 40px), lg (48px). |
| color | Background color when displaying initials or icons. Options: red, orange, amber, yellow, lime, green, emerald, teal, cyan, sky, blue, indigo, violet, purple, fuchsia, pink, rose, auto. Default: none (uses system colors). |
| color:seed | Value used when color="auto" to deterministically generate consistent colors. Useful for using user IDs to generate consistent colors. |
| circle | If present or true, makes the avatar fully circular instead of rounded corners. |
| icon | Name of the icon to display instead of an image or initials. |
| icon:variant | Icon variant to use. Options: outline, solid. Default: solid. |
| tooltip | Text to display in a tooltip when hovering over the avatar. If set to true, uses the name prop as tooltip content. |
| tooltip:position | Position of the tooltip. Options: top, right, bottom, left. Default: top. |
| badge | Content to display as a badge. Can be a string, boolean, or a slot. |
| badge:color | Color of the badge. Options: same color options as the color prop. |
| badge:circle | If present or true, makes the badge fully circular instead of slightly rounded corners. |
| badge:position | Position of the badge. Options: top left, top right, bottom left, bottom right. Default: bottom right. |
| badge:variant | Variant of the badge. Options: solid, outline. Default: solid. |
| as | Element to render the avatar as. Options: button, div (default). |
| href | URL to link to, making the avatar a link element. |

### `flux:avatar.group`

| Prop | Description |
| --- | --- |
| class | CSS classes to apply to the group, including customizing ring colors using *:ring-{color} format. |
