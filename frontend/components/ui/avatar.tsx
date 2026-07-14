import { cn } from '@/lib/utils';

/** Derive up-to-two initials from a display name, like User::initials(). */
export function initialsOf(name: string): string {
  return name
    .split(' ')
    .filter(Boolean)
    .slice(0, 2)
    .map((word) => word[0]!.toUpperCase())
    .join('');
}

export function Avatar({ name, className }: { name: string; className?: string }) {
  return (
    <span
      className={cn(
        'flex size-8 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-fuchsia-500 text-xs font-semibold text-white',
        className,
      )}
    >
      {initialsOf(name)}
    </span>
  );
}
