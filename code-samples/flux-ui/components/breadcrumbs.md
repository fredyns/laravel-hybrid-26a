# Breadcrumbs

Source: https://fluxui.dev/components/breadcrumbs

Help users navigate and understand their place within your application.

## Basic Example

```html
<flux:breadcrumbs>
    <flux:breadcrumbs.item href="#">Home</flux:breadcrumbs.item>
    <flux:breadcrumbs.item href="#">Blog</flux:breadcrumbs.item>
    <flux:breadcrumbs.item>Post</flux:breadcrumbs.item>
</flux:breadcrumbs>
```

## With slashes

```html
<flux:breadcrumbs>
    <flux:breadcrumbs.item href="#" separator="slash">Home</flux:breadcrumbs.item>
    <flux:breadcrumbs.item href="#" separator="slash">Blog</flux:breadcrumbs.item>
    <flux:breadcrumbs.item separator="slash">Post</flux:breadcrumbs.item>
</flux:breadcrumbs>
```

## With icon

```html
<flux:breadcrumbs>
    <flux:breadcrumbs.item href="#" icon="home" />
    <flux:breadcrumbs.item href="#">Blog</flux:breadcrumbs.item>
    <flux:breadcrumbs.item>Post</flux:breadcrumbs.item>
</flux:breadcrumbs>
```

## With ellipsis

```html
<flux:breadcrumbs>
    <flux:breadcrumbs.item href="#" icon="home" />
    <flux:breadcrumbs.item icon="ellipsis-horizontal" />
    <flux:breadcrumbs.item>Post</flux:breadcrumbs.item>
</flux:breadcrumbs>
```

## With ellipsis dropdown

```html
<flux:breadcrumbs>
    <flux:breadcrumbs.item href="#" icon="home" />
    <flux:breadcrumbs.item>
        <flux:dropdown>
            <flux:button icon="ellipsis-horizontal" variant="ghost" size="sm" />
            <flux:navmenu>
                <flux:navmenu.item>Client</flux:navmenu.item>
                <flux:navmenu.item icon="arrow-turn-down-right">Team</flux:navmenu.item>
                <flux:navmenu.item icon="arrow-turn-down-right">User</flux:navmenu.item>
            </flux:navmenu>
        </flux:dropdown>
    </flux:breadcrumbs.item>
    <flux:breadcrumbs.item>Post</flux:breadcrumbs.item>
</flux:breadcrumbs>
```

## Reference

### `flux:breadcrumbs`

| Slot | Description |
| --- | --- |
| default | The breadcrumb items to display. |

### `flux:breadcrumbs.item`

| Prop | Description |
| --- | --- |
| href | URL the breadcrumb item links to. If omitted, renders as non-clickable text. |
| icon | Name of the icon to display before the badge text. |
| icon:variant | Icon variant. Options: outline, solid, mini, micro. Default: mini. |
| separator | Name of the icon to display as the separator. Default: chevron-right. |
