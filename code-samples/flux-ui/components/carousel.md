# Carousel - PRO

Source: https://fluxui.dev/components/carousel

A horizontally scrolling row of slides with snap and edge-aware controls.

## Basic Example

```html
<flux:carousel track:class="rounded-lg overflow-hidden">
    <flux:carousel.slide class="w-full">
        <img src="https://fluxui.dev/img/carousel/seascape.png" class="aspect-16/9 rounded-lg object-cover" />
    </flux:carousel.slide>

    <flux:carousel.slide class="w-full">
        <img src="https://fluxui.dev/img/carousel/woodscape.png" class="aspect-16/9 rounded-lg object-cover" />
    </flux:carousel.slide>

    <flux:carousel.slide class="w-full">
        <img src="https://fluxui.dev/img/carousel/landscape.png" class="aspect-16/9 rounded-lg object-cover" />
    </flux:carousel.slide>
</flux:carousel>
```

## Indicators

Add slide indicators for direct navigation when arrow controls are unnecessary or too prominent.

```html
<flux:carousel class="w-full" indicators :arrows="false">
    <flux:carousel.slide class="w-full">
        <div class="flex flex-col items-center gap-4 text-center">
            <img src="https://fluxui.dev/img/carousel/headshot1.png" class="size-14 rounded-full aspect-square object-cover">
            <blockquote class="text-balance text-zinc-700 dark:text-zinc-300">&ldquo;Flux made it possible to ship a polished UI in days, not weeks.&rdquo;</blockquote>
            <div class="text-sm text-zinc-500"><span class="font-semibold text-zinc-700 dark:text-zinc-200">Graham Whitaker</span> &middot; Founder, Northwind</div>
        </div>
    </flux:carousel.slide>

    <flux:carousel.slide class="w-full">
        <div class="flex flex-col items-center gap-4 text-center">
            <img src="https://fluxui.dev/img/carousel/headshot2.png" class="size-14 rounded-full aspect-square object-cover">
            <blockquote class="text-balance text-zinc-700 dark:text-zinc-300">&ldquo;When I reach for a Flux component, it just works. The defaults are obvious.&rdquo;</blockquote>
            <div class="text-sm text-zinc-500"><span class="font-semibold text-zinc-700 dark:text-zinc-200">Elise Rowan</span> &middot; Engineering Lead, Southline Studio</div>
        </div>
    </flux:carousel.slide>

    <flux:carousel.slide class="w-full">
        <div class="flex flex-col items-center gap-4 text-center">
            <img src="https://fluxui.dev/img/carousel/headshot3.png" class="size-14 rounded-full aspect-square object-cover">
            <blockquote class="text-balance text-zinc-700 dark:text-zinc-300">&ldquo;I stopped writing UI primitives entirely. Flux + Livewire is the closest thing to a content-first dev experience.&rdquo;</blockquote>
            <div class="text-sm text-zinc-500"><span class="font-semibold text-zinc-700 dark:text-zinc-200">Jonah Vale</span> &middot; CTO, Alder & Co.</div>
        </div>
    </flux:carousel.slide>
</flux:carousel>
```

## Autoplay

Automatically advance slides at a fixed interval. Autoplay pauses when the carousel is hovered, focused, or manually controlled.

```html
<flux:carousel autoplay autoplay:interval="3500" indicators>
    <!-- ... -->
</flux:carousel>
```

## External Controls

Use a shared name to place carousel controls outside the carousel layout.

```html
<div class="flex justify-between items-center mb-4">
    <div>
        <flux:heading size="lg">Popular stays</flux:heading>
        <flux:text>Handpicked homes in beautiful places</flux:text>
    </div>
    <div>
        <flux:carousel.controls name="popular-stays" />
    </div>
</div>

<flux:carousel name="popular-stays" class="-mx-6" :arrows="false" track:class="px-6 scroll-px-6">
    <flux:carousel.slide class="w-4/5 sm:w-1/2 md:w-1/3">
        <img src="https://fluxui.dev/img/carousel/house1.png" class="aspect-2/1 rounded-lg object-cover" />
        <flux:heading class="mt-2">Cottage in Mendocino</flux:heading>
        <div class="mt-1 flex gap-0.5 items-center">
            <flux:text class="text-xs"><flux:link href="#" variant="ghost">$680</flux:link> for 2 nights ・</flux:text>
            <div class="flex items-center gap-0.5">
                <flux:icon icon="star" variant="micro" class="size-3" />
                <flux:text class="text-xs text-zinc-800 dark:text-white">5.0</flux:text>
            </div>
        </div>
    </flux:carousel.slide>

    <flux:carousel.slide class="w-4/5 sm:w-1/2 md:w-1/3">
        <img src="https://fluxui.dev/img/carousel/house5.png" class="aspect-2/1 rounded-lg object-cover" />
        <flux:heading class="mt-2">Lodge in the Thousand Islands</flux:heading>
        <div class="mt-1 flex gap-0.5 items-center">
            <flux:text class="text-xs"><flux:link href="#" variant="ghost">$370</flux:link> for 2 nights ・</flux:text>
            <div class="flex items-center gap-0.5">
                <flux:icon icon="star" variant="micro" class="size-3" />
                <flux:text class="text-xs text-zinc-800 dark:text-white">4.96</flux:text>
            </div>
        </div>
    </flux:carousel.slide>
</flux:carousel>
```

## Arrow Position

Change where the built-in arrow controls sit relative to the carousel viewport.

```html
<flux:carousel arrows:position="inside"><!-- ... --></flux:carousel>
<flux:carousel arrows:position="overlap"><!-- ... --></flux:carousel>
<flux:carousel arrows:position="outside"><!-- ... --></flux:carousel>
```

## Fade

Add edge fades to hint that more slides are available without covering the arrow controls.

```html
<flux:carousel fade class="-mx-6" track:class="px-6 scroll-px-6">
    <!-- ... -->
</flux:carousel>
```

## Page Advance

Move by the number of visible slides instead of one slide at a time.

```html
<flux:carousel advance="page">
    <!-- ... -->
</flux:carousel>
```

## Reference

### `flux:carousel`

| Prop              | Description |
| ----------------- | ----------- |
| arrows            | If true, shows previous and next arrow controls over the carousel. Default: true. |
| arrows:position   | Position for the built-in arrow controls. Options: `inside`, `overlap`, `outside`. Default: `inside`. |
| arrows:class      | Additional classes applied to the built-in arrow controls. |
| autoplay          | If true, automatically advances slides. Pauses on hover, focus, or manual interaction, and is disabled when reduced motion is preferred. |
| autoplay:interval | Time between autoplay advances in milliseconds. Default: `5000`. |
| indicators        | If true, shows slide indicators below the carousel. Default: false. |
| disabled          | If true, disables carousel controls and indicators. |
| fade              | If true, fades the scrollable track edges when additional slides are overflowed. |
| snap              | Scroll snap behavior. Options: `proximity`, `mandatory`. Default: `proximity`. |
| scroll            | Arrow and indicator scroll behavior. Options: `smooth`, `instant`. Default: `smooth`. |
| advance           | How many slides arrow controls move. Options: `slide`, `page`. Default: `slide`. |
| name              | Unique name used to connect the carousel with external controls. |
| track:class       | Additional classes applied to the scrollable track. |

### `flux:carousel.slide`

| Prop  | Description |
| ----- | ----------- |
| class | Classes applied to the slide. Use width classes like `w-full`, `w-4/5`, or `md:w-1/3` to control how many slides are visible at once. |

### `flux:carousel.controls`

| Prop | Description |
| ---- | ----------- |
| name | Name of the carousel these external controls should operate on. |
