import * as React from 'react';
import { cn } from '@/lib/utils';

function Table({ className, ...props }: React.ComponentProps<'table'>) {
  return (
    <div className="w-full overflow-x-auto">
      <table className={cn('w-full text-sm', className)} {...props} />
    </div>
  );
}

function TableHeader({ className, ...props }: React.ComponentProps<'thead'>) {
  return <thead className={cn('border-b border-zinc-200 dark:border-zinc-700', className)} {...props} />;
}

function TableBody({ className, ...props }: React.ComponentProps<'tbody'>) {
  return <tbody className={cn('divide-y divide-zinc-100 dark:divide-zinc-700/60', className)} {...props} />;
}

function TableRow({ className, ...props }: React.ComponentProps<'tr'>) {
  return <tr className={cn('hover:bg-zinc-50 dark:hover:bg-zinc-700/30', className)} {...props} />;
}

function TableHead({ className, ...props }: React.ComponentProps<'th'>) {
  return (
    <th
      className={cn(
        'px-3 py-3 text-start text-sm font-medium text-zinc-800 first:ps-0 last:pe-0 dark:text-zinc-200',
        className,
      )}
      {...props}
    />
  );
}

function TableCell({ className, ...props }: React.ComponentProps<'td'>) {
  return <td className={cn('px-3 py-4 first:ps-0 last:pe-0', className)} {...props} />;
}

export { Table, TableHeader, TableBody, TableRow, TableHead, TableCell };
