# Pagination

Source: https://fluxui.dev/components/pagination

## Basic Example

```html
<!-- $orders = Order::paginate() -->
<flux:pagination :paginator="$orders" />
```

## Simple paginator

Use the simple paginator when working with large datasets where counting the total number of results would be expensive. The simple paginator provides "Previous" and "Next" buttons without displaying the total number of pages or records.

```html
<!-- $orders = Order::simplePaginate() -->
<flux:pagination :paginator="$orders" />
```

## Large result set

When working with large result sets, the pagination component automatically adapts to show a reasonable number of page links. It shows the first and last pages, along with a window of pages around the current page, and adds ellipses for any gaps to ensure efficient navigation through numerous pages.

```html
<!-- $orders = Order::paginate(5) -->
<flux:pagination :paginator="$orders" />
```

## Reference

### `flux:pagination`

| Prop | Description |
| --- | --- |
| `paginator` | The paginator instance to display. |
