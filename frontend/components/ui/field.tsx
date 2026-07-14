import * as React from 'react';
import { AlertCircle } from 'lucide-react';
import { cn } from '@/lib/utils';

/** Renders the first validation error for a TanStack Form field, if any. */
export function FieldError({ errors }: { errors: unknown[] }) {
  const message = getErrorText(errors);
  if (!message) {
    return null;
  }
  return <p className="text-sm font-medium text-destructive">{message}</p>;
}

/** Top-level form/server error banner. */
export function FormError({ message }: { message: string | null }) {
  if (!message) {
    return null;
  }
  return (
    <div
      role="alert"
      className={cn(
        'flex items-start gap-2 rounded-md border border-destructive/50 bg-destructive/10 p-3 text-sm text-destructive',
      )}
    >
      <AlertCircle className="mt-0.5 size-4 shrink-0" />
      <span>{message}</span>
    </div>
  );
}

/** Groups a label, control and error message with consistent spacing. */
export function Field({ children, className }: { children: React.ReactNode; className?: string }) {
  return <div className={cn('space-y-2', className)}>{children}</div>;
}

/**
 * TanStack Form errors can be plain strings or Standard Schema issue objects
 * (`{ message }`). Normalise to the first readable message.
 */
function getErrorText(errors: unknown[]): string | undefined {
  const first = errors.find(Boolean);
  if (!first) {
    return undefined;
  }
  if (typeof first === 'string') {
    return first;
  }
  if (typeof first === 'object' && 'message' in first) {
    return String((first as { message: unknown }).message);
  }
  return String(first);
}
