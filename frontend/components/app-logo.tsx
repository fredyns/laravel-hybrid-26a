import { cn } from '@/lib/utils';

export const APP_NAME = 'LiveWireKit';

/** Brand mark, ported from resources/views/components/app-logo-icon.blade.php. */
export function AppLogoIcon({ className }: { className?: string }) {
  return (
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 42" className={className}>
      <path
        fill="currentColor"
        fillRule="evenodd"
        clipRule="evenodd"
        d="M17.2 5.633 8.6.855 0 5.633v26.51l16.2 9 16.2-9v-8.442l7.6-4.223V9.856l-8.6-4.777-8.6 4.777V18.3l-5.6 3.111V5.633ZM38 18.301l-5.6 3.11v-6.157l5.6-3.11V18.3Zm-1.06-7.856-5.54 3.078-5.54-3.079 5.54-3.078 5.54 3.079ZM24.8 18.3v-6.157l5.6 3.111v6.158L24.8 18.3Zm-1 1.732 5.54 3.078-13.14 7.302-5.54-3.078 13.14-7.3v-.002Zm-16.2 7.89 7.6 4.222V38.3L2 30.966V7.92l5.6 3.111v16.892ZM8.6 9.3 3.06 6.222 8.6 3.143l5.54 3.08L8.6 9.3Zm21.8 15.51-13.2 7.334V38.3l13.2-7.334v-6.156ZM9.6 11.034l5.6-3.11v14.6l-5.6 3.11v-14.6Z"
      />
    </svg>
  );
}

/** Gradient logo tile on its own, used where space is tight (collapsed rail). */
export function AppLogoMark({ className }: { className?: string }) {
  return (
    <span
      className={cn(
        'flex aspect-square size-8 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 via-purple-500 to-fuchsia-500 shadow-sm shadow-indigo-500/40',
        className,
      )}
    >
      <AppLogoIcon className="size-5 fill-current text-white" />
    </span>
  );
}

/** Gradient logo tile + app name, as used in the sidebar and headers. */
export function AppLogo({ className, iconClassName }: { className?: string; iconClassName?: string }) {
  return (
    <span className={cn('flex items-center gap-2', className)}>
      <AppLogoMark className={iconClassName} />
      <span className="truncate text-sm font-semibold">{APP_NAME}</span>
    </span>
  );
}
