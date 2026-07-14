import { Link, Outlet, useLocation } from '@tanstack/react-router';
import { cn } from '@/lib/utils';

const tabs = [
  { label: 'Profile', to: '/settings/profile' },
  { label: 'Security', to: '/settings/security' },
  { label: 'Appearance', to: '/settings/appearance' },
];

/** Ported from resources/views/pages/settings/layout.blade.php. */
export function SettingsLayout() {
  const { pathname } = useLocation();

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-semibold tracking-tight">Settings</h1>
        <p className="mt-1 text-sm text-muted-foreground">Manage your profile and account settings</p>
      </div>

      <div className="flex flex-col gap-8 border-t border-zinc-200 pt-6 lg:flex-row dark:border-zinc-700">
        <nav aria-label="Settings" className="flex w-full shrink-0 gap-1 lg:w-52 lg:flex-col">
          {tabs.map((tab) => {
            const current = pathname.startsWith(tab.to);
            return (
              <Link
                key={tab.to}
                to={tab.to}
                className={cn(
                  'rounded-lg px-4 py-2 text-sm transition-colors',
                  current
                    ? 'bg-zinc-100 font-medium text-indigo-600 dark:bg-zinc-700 dark:text-indigo-300'
                    : 'text-zinc-600 hover:bg-zinc-50 dark:text-zinc-300 dark:hover:bg-zinc-700/50',
                )}
              >
                {tab.label}
              </Link>
            );
          })}
        </nav>

        <div className="w-full max-w-2xl flex-1">
          <Outlet />
        </div>
      </div>
    </div>
  );
}

/** Section heading + description used across the settings screens. */
export function SettingsSection({
  title,
  description,
  children,
}: {
  title: string;
  description: string;
  children?: React.ReactNode;
}) {
  return (
    <section className="space-y-4">
      <div>
        <h2 className="font-semibold">{title}</h2>
        <p className="mt-1 text-sm text-muted-foreground">{description}</p>
      </div>
      {children}
    </section>
  );
}
