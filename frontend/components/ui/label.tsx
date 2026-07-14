import * as React from 'react';
import { Root as LabelPrimitive } from '@radix-ui/react-label';
import { cn } from '@/lib/utils';

function Label({
  className,
  ...props
}: React.ComponentProps<typeof LabelPrimitive>) {
  return (
    <LabelPrimitive
      className={cn(
        'text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70',
        className,
      )}
      {...props}
    />
  );
}

export { Label };
