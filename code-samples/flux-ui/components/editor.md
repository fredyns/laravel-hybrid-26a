# Rich text editor - PRO

Source: https://fluxui.dev/components/editor

A basic rich text editor for your application. Built using [ProseMirror](https://prosemirror.net/) and [Tiptap](https://tiptap.dev/).

>Because of large external dependencies, the editor's JavaScript is not included in the core Flux bundle. It will be loaded on-the-fly as a separate JS file when you use the flux:editor component.

## Basic Example

```html
<flux:editor wire:model="content" label="…" description="…" />
```

## Toolbar
Flux's editor toolbar is both keyboard/screen-reader accessible and completely customizable to suit your application's needs.

## Configuring items

```html
<flux:editor toolbar="heading | bold italic underline | align ~ undo redo" />
```
>You may have noticed the `|` and `~` characters in the toolbar configuration. These are shorthand for `separator` and `spacer` respectively.

The following toolbar items are available:
- heading
- bold
- italic
- strike
- underline
- bullet
- ordered
- blockquote
- subscript
- superscript
- highlight
- link
- code
- undo
- redo

## Custom items
You can add your own toolbar items by adding new files to the `resources/views/flux/editor` directory in your project.

```
resources/
└── views/
    └── flux/
        └── editor/
            └── copy.blade.php
```

Here's an example of what a custom "Copy to clipboard" item in a blade file might look like:

```html
<flux:tooltip content="{{ __('Copy to clipboard') }}" class="contents">
    <flux:editor.button x-on:click="navigator.clipboard?.writeText($el.closest('[data-flux-editor]').value); $el.setAttribute('data-copied', ''); setTimeout(() => $el.removeAttribute('data-copied'), 2000)">
        <flux:icon.clipboard variant="outline" class="[[data-copied]_&]:hidden size-5!" />
        <flux:icon.clipboard-document-check variant="outline" class="hidden [[data-copied]_&]:block size-5!" />
    </flux:editor.button>
</flux:tooltip>
```

Now you can reference your new component by name in any toolbar configuration like so:
```html
<flux:editor toolbar="heading | […] | align ~ copy" />
```

## Customization
If you have deeper customization needs, you can compose your own editor component. Here's an example of putting a custom dropdown menu in an editor's toolbar:

```html
<flux:editor>
    <flux:editor.toolbar>
        <flux:editor.heading />
        <flux:editor.separator />
        <flux:editor.bold />
        <flux:editor.italic />
        <flux:editor.strike />
        <flux:editor.separator />
        <flux:editor.bullet />
        <flux:editor.ordered />
        <flux:editor.blockquote />
        <flux:editor.separator />
        <flux:editor.link />
        <flux:editor.separator />
        <flux:editor.align />
        <flux:editor.spacer />
        <flux:dropdown position="bottom end" offset="-15">
            <flux:editor.button icon="ellipsis-horizontal" tooltip="More" />
            <flux:menu>
                <flux:menu.item wire:click="…" icon="arrow-top-right-on-square">Preview</flux:menu.item>
                <flux:menu.item wire:click="…" icon="arrow-down-tray">Export</flux:menu.item>
                <flux:menu.item wire:click="…" icon="share">Share</flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </flux:editor.toolbar>
    <flux:editor.content />
</flux:editor>
```

## Height

```html
<flux:editor class="**:data-[slot=content]:min-h-[100px]!" />
```

>The `[&_[data-slot=content]]:` selector targets a child element with a `data-slot="content"` attribute.

>This is an advanced Tailwind technique used to style a nested element inline without needing direct access to it.

## Localization
If you need to localize the editor's aria-label or tooltip copy, you'll need to register the following translation keys in one of your app's lang files.

Here's an example of supporting Spanish localization:
```lang/es.json
{
    "Rich text editor": "Editor de texto enriquecido",
    "Formatting": "Formato",
    "Text": "Texto",
    "Heading 1": "Encabezado 1",
    "Heading 2": "Encabezado 2",
    "Heading 3": "Encabezado 3",
    "Styles": "Estilos",
    "Bold": "Negrita",
    "Italic": "Cursiva",
    "Underline": "Subrayado",
    "Strikethrough": "Tachado",
    "Subscript": "Subíndice",
    "Superscript": "Superíndice",
    "Highlight": "Resaltar",
    "Code": "Código",
    "Bullet list": "Lista con viñetas",
    "Ordered list": "Lista numerada",
    "Blockquote": "Cita",
    "Insert link": "Insertar enlace",
    "Unlink": "Quitar enlace",
    "Align": "Alinear",
    "Left": "Izquierda",
    "Center": "Centro",
    "Right": "Derecha",
    "Undo": "Deshacer",
    "Redo": "Rehacer"
}
```

## Extensions
Tiptap has a wide range of extensions that can be used to add custom functionality to the editor.

The following extensions are already installed:
- Highlight
- Link
- Placeholder
- StarterKit
- Superscript
- Subscript
- TextAlign
- Underline

## Set up listener
To do this, you'll first need to create a listener for the `flux:editor` event in the `<head>` tag in your layout.

```html
<head>
    ...
</head>
```

Or you can add the listener to your `app.js` file.

```html
...
document.addEventListener('flux:editor', (e) => {
    ...
})
```

## Registering extensions
Once you have the listener set up, you can add extensions by passing an array of extensions to the `registerExtensions` method supplied by the `flux:editor` event.

>If an extension already exists, it will be replaced.

```html
import Youtube from 'https://cdn.jsdelivr.net/npm/@tiptap/extension-youtube@2.11.7/+esm'

document.addEventListener('flux:editor', (e) => {
    e.detail.registerExtensions([
        Youtube.configure({
            controls: false,
            nocookie: true,
        }),
    ])
})
```

## Disabling extensions
If you need to disable an extension that comes with the editor, you can disable an extension by calling the `disableExtension` method supplied by the `flux:editor` event and passing through the name of the extension.

```html
document.addEventListener('flux:editor', (e) => {
    e.detail.disableExtension('underline')
})
```

## Accessing the instance

Sometimes you may need to access the Tiptap instance directly to register event listeners or otherwise interact with with the instance.
You can do so by passing a callback to the `init` method supplied by the `flux:editor` event and accessing the `editor` instance from within the callback.

>The `init` callback will be called when Tiptap's `beforeCreate` event is fired. You can find more details about Tiptap's events in the [Tiptap documentation](https://tiptap.dev/docs/editor/api/events).

```html
document.addEventListener('flux:editor', (e) => {
    e.detail.init(({ editor }) => {
        editor.on('create', () => {})
        editor.on('update', () => {})
        editor.on('selectionUpdate', () => {})
        editor.on('transaction', () => {})
        editor.on('focus', () => {})
        editor.on('blur', () => {})
        editor.on('destroy', () => {})
        editor.on('drop', () => {})
        editor.on('paste', () => {})
        editor.on('contentError', () => {})
    })
})
```

## Reference

### `flux:editor`

| Prop | Description |
| --- | --- |
| wire:model | Binds the editor to a Livewire property. See the wire:model documentation for more information. |
| value | Initial content for the editor. Used when not binding with wire:model. |
| label | Label text displayed above the editor. When provided, wraps the editor in a flux:field component with an adjacent flux:label component. See the field component. |
| description | Help text displayed below the editor. When provided alongside label, appears between the label and editor within the flux:field wrapper. See the field component. |
| description:trailing | The description provided will be displayed below the editor instead of above it. |
| badge | Badge text displayed at the end of the flux:label component when the label prop is provided. |
| placeholder | Placeholder text displayed when the editor is empty. |
| toolbar | Space-separated list of toolbar items to display. Use \| for separator and ~ for spacer. By default, includes heading, bold, italic, strike, bullet, ordered, blockquote, link, and align tools. |
| disabled | Prevents user interaction with the editor. |
| invalid | Applies error styling to the editor. |

| Slot | Description |
| --- | --- |
| default | The editor content and toolbar components. If omitted, the standard toolbar and an empty content area will be used. |

| Attribute | Description |
| --- | --- |
| data-flux-editor | Applied to the root element for styling and identification. |

### `flux:editor.toolbar`

| Prop | Description |
| --- | --- |
| items | Space-separated list of toolbar items to display. Use \| for separator and ~ for spacer. If not provided, displays the default toolbar. |

| Slot | Description |
| --- | --- |
| default | The toolbar items, separators, and spacers. Use this slot to create a completely custom toolbar. |

### `flux:editor.button`

| Prop | Description |
| --- | --- |
| icon | Name of the icon to display in the button. |
| iconVariant | The variant of the icon to display. Options: mini, micro, outline. Default: mini (without slot) or micro (with slot). |
| tooltip | Text to display in a tooltip when hovering over the button. |
| disabled | Prevents interaction with the button. |

| Slot | Description |
| --- | --- |
| default | Content to display inside the button. If provided alongside an icon, the icon will be displayed before this content. |

### `flux:editor.content`

| Slot | Description |
| --- | --- |
| default | The initial HTML content for the editor. This content will be processed and managed by the editor. |

### `Toolbar Items`

| Component | Description |
| --- | --- |
| heading | Heading level selector. |
| bold | Bold text formatting. |
| italic | Italic text formatting. |
| strike | Strikethrough text formatting. |
| underline | Underline text formatting. |
| bullet | Bulleted list. |
| ordered | Numbered list. |
| blockquote | Block quote formatting. |
| code | Code block formatting. |
| link | Link insertion. |
| align | Text alignment options. |
| undo | Undo last action. |
| redo | Redo last action. |
