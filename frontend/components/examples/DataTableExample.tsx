import { type ColumnDef } from '@tanstack/react-table';
import { DataTable } from '@/components/ui/table';

interface Product {
  id: string;
  name: string;
  price: number;
  stock: number;
}

const products: Product[] = [
  { id: '1', name: 'Laptop', price: 999, stock: 5 },
  { id: '2', name: 'Mouse', price: 29, stock: 150 },
  { id: '3', name: 'Keyboard', price: 89, stock: 45 },
  { id: '4', name: 'Monitor', price: 299, stock: 12 },
  { id: '5', name: 'Headphones', price: 199, stock: 78 },
  { id: '6', name: 'USB Cable', price: 9, stock: 500 },
  { id: '7', name: 'SSD', price: 149, stock: 30 },
  { id: '8', name: 'RAM', price: 79, stock: 60 },
];

/**
 * Example: Client-side DataTable with TanStack Table
 *
 * Features:
 * - Click column headers to sort
 * - Client-side pagination (10 items per page)
 * - Fully typed columns
 *
 * Use cases:
 * - Data is already loaded (not from server)
 * - Need client-side sorting/filtering
 * - Dataset is small to medium (< 10K rows)
 *
 * For server-side pagination, see UsersPage.tsx instead
 */
export function DataTableExample() {
  const columns: ColumnDef<Product>[] = [
    {
      accessorKey: 'name',
      header: 'Product',
      cell: (info) => info.getValue(),
    },
    {
      accessorKey: 'price',
      header: 'Price',
      cell: (info) => `$${(info.getValue() as number).toFixed(2)}`,
    },
    {
      accessorKey: 'stock',
      header: 'Stock',
      cell: (info) => {
        const stock = info.getValue() as number;
        return (
          <span className={stock > 50 ? 'text-green-600' : stock > 10 ? 'text-yellow-600' : 'text-red-600'}>
            {stock}
          </span>
        );
      },
    },
  ];

  return (
    <div className="space-y-4">
      <div>
        <h2 className="text-lg font-semibold">Product Inventory</h2>
        <p className="text-sm text-zinc-600 dark:text-zinc-400">
          Click column headers to sort. This example demonstrates client-side pagination with TanStack Table.
        </p>
      </div>

      <DataTable columns={columns} data={products} pageSize={3} enableSorting={true} enablePagination={true} />
    </div>
  );
}
