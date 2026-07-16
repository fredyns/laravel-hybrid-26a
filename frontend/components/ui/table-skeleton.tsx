import { Skeleton } from './skeleton';

interface TableSkeletonProps {
  rows?: number;
  columns?: number;
}

function TableSkeleton({ rows = 5, columns = 4 }: TableSkeletonProps) {
  return (
    <div className="w-full overflow-x-auto border border-zinc-200 dark:border-zinc-700 rounded-lg">
      <table className="w-full text-sm">
        <thead className="border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900">
          <tr>
            {Array.from({ length: columns }).map((_, i) => (
              <th
                key={i}
                className="px-3 py-3 text-start first:ps-0 last:pe-0"
              >
                <Skeleton className="h-4 w-24 rounded" />
              </th>
            ))}
          </tr>
        </thead>
        <tbody className="divide-y divide-zinc-100 dark:divide-zinc-700/60">
          {Array.from({ length: rows }).map((_, rowIndex) => (
            <tr key={rowIndex}>
              {Array.from({ length: columns }).map((_, colIndex) => (
                <td
                  key={colIndex}
                  className="px-3 py-4 first:ps-0 last:pe-0"
                >
                  <Skeleton className="h-4 w-full rounded" />
                </td>
              ))}
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}

interface RowSkeletonProps {
  columns?: number;
}

function RowSkeleton({ columns = 4 }: RowSkeletonProps) {
  return (
    <tr>
      {Array.from({ length: columns }).map((_, i) => (
        <td key={i} className="px-3 py-4 first:ps-0 last:pe-0">
          <Skeleton className="h-4 w-full rounded" />
        </td>
      ))}
    </tr>
  );
}

export { TableSkeleton, RowSkeleton };
