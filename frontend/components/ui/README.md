# UI Components Guide

## Table Components

### 1. Basic Table (HTML Wrapper)
For simple, unstyled table structures.

```tsx
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/components/ui/table';

export function SimpleTable() {
  return (
    <Table>
      <TableHeader>
        <TableRow>
          <TableHead>Name</TableHead>
          <TableHead>Email</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableRow>
          <TableCell>John Doe</TableCell>
          <TableCell>john@example.com</TableCell>
        </TableRow>
      </TableBody>
    </Table>
  );
}
```

### 2. DataTable (TanStack Table)
For powerful client-side tables with sorting, filtering, and pagination.

**Best for:**
- Data already loaded in memory
- Small to medium datasets (< 10K rows)
- Need sorting, filtering, column toggling

```tsx
import { DataTable } from '@/components/ui/table';
import { ColumnDef } from '@tanstack/react-table';

interface User {
  id: string;
  name: string;
  email: string;
}

export function UserTable() {
  const columns: ColumnDef<User>[] = [
    {
      accessorKey: 'name',
      header: 'Name',
      cell: (info) => info.getValue(),
    },
    {
      accessorKey: 'email',
      header: 'Email',
      cell: (info) => info.getValue(),
    },
  ];

  const data: User[] = [
    { id: '1', name: 'John', email: 'john@example.com' },
    { id: '2', name: 'Jane', email: 'jane@example.com' },
  ];

  return (
    <DataTable
      columns={columns}
      data={data}
      enablePagination={true}
      enableSorting={true}
      pageSize={10}
    />
  );
}
```

### 3. VirtualList (TanStack Virtual)
For rendering very large lists efficiently.

**Best for:**
- 10K+ rows
- Infinite scrolling
- Performance-critical lists

```tsx
import { VirtualList } from '@/components/ui/virtual-list';

export function LargeList() {
  const items = Array.from({ length: 10000 }, (_, i) => ({
    id: i,
    name: `Item ${i}`,
  }));

  return (
    <VirtualList
      items={items}
      itemHeight={50}
      containerClassName="h-screen"
      renderItem={(item) => (
        <div className="px-4 py-2 border-b">
          {item.name}
        </div>
      )}
    />
  );
}
```

## Usage Patterns

### With React Query (Server-side Data)
For server-side pagination like in `UsersPage`:
- Keep using the basic `Table` component
- Let the server handle pagination/sorting
- Query result is already optimized with React Query caching

```tsx
const { data } = useUsersList({ page: 1, per_page: 10 });
// Use basic Table component for rendering
```

### With Loaded Data (Client-side)
For data already in memory:
- Use `DataTable` for features like sorting/filtering
- React Query can still cache the data
- Client handles sorting/pagination

```tsx
const { data: users } = useQuery({
  queryKey: ['all-users'],
  queryFn: () => fetchAllUsers(), // Load all data once
});

return <DataTable columns={columns} data={users} />;
```

### For Performance (Huge Lists)
For virtualized rendering:
- Combine `DataTable` + `VirtualList`
- Or use `VirtualList` standalone with custom rendering

## Current Implementation Notes

- `UsersPage.tsx` uses server-side pagination (optimal for large datasets)
- Can be upgraded to DataTable if client-side features are needed
- VirtualList is available for any page showing 1000+ items
