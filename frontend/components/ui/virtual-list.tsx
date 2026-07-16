import * as React from 'react';
import { useVirtualizer } from '@tanstack/react-virtual';
import { cn } from '@/lib/utils';

type VirtualListProps<T> = {
  items: T[];
  itemHeight: number;
  renderItem: (item: T, index: number) => React.ReactNode;
  className?: string;
  containerClassName?: string;
  overscan?: number;
};

function VirtualList<T>({
  items,
  itemHeight,
  renderItem,
  className,
  containerClassName,
  overscan = 5,
}: VirtualListProps<T>) {
  const parentRef = React.useRef<HTMLDivElement>(null);

  const virtualizer = useVirtualizer({
    count: items.length,
    getScrollElement: () => parentRef.current,
    estimateSize: () => itemHeight,
    overscan,
  });

  return (
    <div
      ref={parentRef}
      className={cn('overflow-y-auto', containerClassName)}
    >
      <div
        style={{
          height: `${virtualizer.getTotalSize()}px`,
        }}
        className="relative w-full"
      >
        {virtualizer.getVirtualItems().map((virtualItem) => (
          <div
            key={virtualItem.key}
            data-index={virtualItem.index}
            style={{
              transform: `translateY(${virtualItem.start}px)`,
            }}
            className={cn('absolute top-0 left-0 w-full', className)}
          >
            {renderItem(items[virtualItem.index], virtualItem.index)}
          </div>
        ))}
      </div>
    </div>
  );
}

export { VirtualList, type VirtualListProps };
