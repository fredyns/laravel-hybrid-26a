import { useId } from 'react';

/** Diagonal-stripe placeholder, ported from resources/views/components/placeholder-pattern.blade.php. */
export function PlaceholderPattern({ className }: { className?: string }) {
  const id = useId();
  return (
    <svg className={className} fill="none">
      <defs>
        <pattern id={id} x="0" y="0" width="8" height="8" patternUnits="userSpaceOnUse">
          <path d="M-1 5L5 -1M3 9L8.5 3.5" strokeWidth="0.5" />
        </pattern>
      </defs>
      <rect stroke="none" fill={`url(#${id})`} width="100%" height="100%" />
    </svg>
  );
}
