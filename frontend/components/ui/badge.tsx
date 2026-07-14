import * as React from 'react';
import { cva, type VariantProps } from 'class-variance-authority';
import { cn } from '@/lib/utils';

const badgeVariants = cva(
  'inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-medium whitespace-nowrap',
  {
    variants: {
      variant: {
        default: 'bg-zinc-100 text-zinc-700 dark:bg-zinc-700 dark:text-zinc-200',
        amber: 'bg-amber-100 text-amber-800 dark:bg-amber-400/20 dark:text-amber-300',
        indigo: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-400/20 dark:text-indigo-300',
        emerald: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-400/20 dark:text-emerald-300',
      },
    },
    defaultVariants: {
      variant: 'default',
    },
  },
);

export interface BadgeProps
  extends React.HTMLAttributes<HTMLSpanElement>,
    VariantProps<typeof badgeVariants> {}

export function Badge({ className, variant, ...props }: BadgeProps) {
  return <span className={cn(badgeVariants({ variant, className }))} {...props} />;
}
