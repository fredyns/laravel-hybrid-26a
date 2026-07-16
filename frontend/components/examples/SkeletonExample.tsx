import { useState } from 'react';
import { TableSkeleton } from '@/components/ui/table-skeleton';
import { Button } from '@/components/ui/button';

/**
 * Example: Table Skeleton Loading State
 *
 * Demonstrates:
 * - Shimmering skeleton animation
 * - Proper use in loading states
 * - Matches table column structure
 *
 * Applied to:
 * - NotificationsPage (4 columns)
 * - RolesPage (6 columns)
 * - UsersPage (4 columns)
 */
export function SkeletonExample() {
  const [isLoading, setIsLoading] = useState(false);

  const handleLoad = () => {
    setIsLoading(true);
    setTimeout(() => setIsLoading(false), 2500);
  };

  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-lg font-semibold">Skeleton Loading State</h2>
        <p className="text-sm text-zinc-600 dark:text-zinc-400">
          Shimmering skeleton displayed while data loads. Click to simulate loading state.
        </p>
      </div>

      <Button onClick={handleLoad} variant={isLoading ? 'outline' : 'default'}>
        {isLoading ? 'Loading...' : 'Simulate Loading'}
      </Button>

      {isLoading ? (
        <TableSkeleton rows={5} columns={4} />
      ) : (
        <div className="w-full overflow-x-auto border border-zinc-200 dark:border-zinc-700 rounded-lg">
          <table className="w-full text-sm">
            <thead className="border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900">
              <tr>
                <th className="px-3 py-3 text-start font-medium text-zinc-800 dark:text-zinc-200">
                  Name
                </th>
                <th className="px-3 py-3 text-start font-medium text-zinc-800 dark:text-zinc-200">
                  Email
                </th>
                <th className="px-3 py-3 text-start font-medium text-zinc-800 dark:text-zinc-200">
                  Status
                </th>
                <th className="px-3 py-3 text-end font-medium text-zinc-800 dark:text-zinc-200">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody className="divide-y divide-zinc-100 dark:divide-zinc-700/60">
              {[1, 2, 3, 4, 5].map((i) => (
                <tr key={i} className="hover:bg-zinc-50 dark:hover:bg-zinc-700/30">
                  <td className="px-3 py-4 font-medium">User {i}</td>
                  <td className="px-3 py-4 text-zinc-600 dark:text-zinc-400">user{i}@example.com</td>
                  <td className="px-3 py-4">Active</td>
                  <td className="px-3 py-4 text-end">
                    <button className="text-sm text-indigo-600 hover:text-indigo-700">View</button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}

      <div className="text-xs text-zinc-500 dark:text-zinc-400 space-y-2">
        <p>
          💡 <strong>How it works:</strong> When loading starts, skeleton placeholders appear with a smooth shimmer
          animation. This provides better perceived performance compared to a blank spinner.
        </p>
        <p>
          🎯 <strong>Applied to:</strong> NotificationsPage, RolesPage, and UsersPage show skeleton loaders during
          initial data fetch.
        </p>
      </div>
    </div>
  );
}
