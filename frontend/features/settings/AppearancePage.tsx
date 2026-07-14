import { Monitor, Moon, Sun } from 'lucide-react';
import { setAppearance, useAppearance, type Appearance } from '@/lib/appearance';
import { SettingsSection } from '@/features/settings/SettingsLayout';
import { cn } from '@/lib/utils';

const options: { value: Appearance; label: string; icon: typeof Sun }[] = [
  { value: 'light', label: 'Light', icon: Sun },
  { value: 'dark', label: 'Dark', icon: Moon },
  { value: 'system', label: 'System', icon: Monitor },
];

/** Ported from the Livewire Appearance settings page — fully functional. */
export function AppearancePage() {
  const appearance = useAppearance();

  return (
    <SettingsSection title="Appearance" description="Update the appearance settings for your account">
      <div className="inline-flex rounded-lg bg-zinc-100 p-1 dark:bg-zinc-700">
        {options.map(({ value, label, icon: Icon }) => (
          <button
            key={value}
            type="button"
            onClick={() => setAppearance(value)}
            className={cn(
              'flex cursor-pointer items-center gap-2 rounded-md px-8 py-1.5 text-sm font-medium transition-colors',
              appearance === value
                ? 'bg-white text-zinc-900 shadow-sm dark:bg-zinc-800 dark:text-white'
                : 'text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-zinc-200',
            )}
          >
            <Icon className="size-4" />
            {label}
          </button>
        ))}
      </div>
    </SettingsSection>
  );
}
