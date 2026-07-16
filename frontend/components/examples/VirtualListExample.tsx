import { VirtualList } from '@/components/ui/virtual-list';

interface Item {
  id: number;
  name: string;
  value: number;
}

/**
 * Example: VirtualList with 10,000 items
 *
 * Features:
 * - Only renders visible items (efficient)
 * - Smooth scrolling with 10K+ rows
 * - Fixed item height
 *
 * Use cases:
 * - Very large lists (1000+ items)
 * - Infinite scrolling
 * - Performance-critical rendering
 *
 * Why virtualization?
 * - Without: 10K DOM nodes = SLOW
 * - With: ~20 visible DOM nodes = FAST
 */
export function VirtualListExample() {
  const items: Item[] = Array.from({ length: 10000 }, (_, i) => ({
    id: i,
    name: `Item ${i + 1}`,
    value: Math.floor(Math.random() * 1000),
  }));

  return (
    <div className="space-y-4">
      <div>
        <h2 className="text-lg font-semibold">Virtual List (10,000 items)</h2>
        <p className="text-sm text-zinc-600 dark:text-zinc-400">
          Scroll smoothly through 10,000 items using TanStack Virtual. Only visible items are rendered to DOM.
        </p>
      </div>

      <VirtualList
        items={items}
        itemHeight={44}
        containerClassName="h-96 border border-zinc-200 dark:border-zinc-700 rounded-lg"
        renderItem={(item) => (
          <div className="px-4 py-2.5 border-b border-zinc-100 dark:border-zinc-700/50 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 flex justify-between items-center">
            <span className="font-medium text-sm">{item.name}</span>
            <span className="text-xs text-zinc-500 dark:text-zinc-400">${item.value}</span>
          </div>
        )}
      />

      <p className="text-xs text-zinc-500 dark:text-zinc-400">
        💡 Tip: This is much faster than rendering 10K DOM nodes. Try combining with DataTable for a virtualized data
        grid.
      </p>
    </div>
  );
}
