import * as React from 'react';
import { useState } from 'react';
import { Eye, EyeOff } from 'lucide-react';
import { Input } from '@/components/ui/input';
import { cn } from '@/lib/utils';

/** Password input with a Flux-style visibility toggle. */
export function PasswordInput({ className, ...props }: Omit<React.ComponentProps<'input'>, 'type'>) {
  const [show, setShow] = useState(false);
  return (
    <div className="relative">
      <Input type={show ? 'text' : 'password'} className={cn('pe-10', className)} {...props} />
      <button
        type="button"
        aria-label="Toggle password visibility"
        tabIndex={-1}
        onClick={() => setShow((value) => !value)}
        className="absolute end-3 top-1/2 -translate-y-1/2 cursor-pointer text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200"
      >
        {show ? <EyeOff className="size-4" /> : <Eye className="size-4" />}
      </button>
    </div>
  );
}
