import { Monitor, Moon, Sun } from 'lucide-react';
import { setAppearance, useAppearance } from '@/lib/appearance';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

export function ThemeDropdown() {
  const appearance = useAppearance();
  const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  const dark = appearance === 'dark' || (appearance === 'system' && systemDark);

  return (
    <DropdownMenu>
      <DropdownMenuTrigger
        aria-label="Color scheme"
        className="flex size-8 cursor-pointer items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-accent hover:text-foreground"
      >
        {dark ? <Moon className="size-4" /> : <Sun className="size-4" />}
      </DropdownMenuTrigger>
      <DropdownMenuContent align="end">
        <DropdownMenuItem onSelect={() => setAppearance('light')}>
          <Sun /> Light
        </DropdownMenuItem>
        <DropdownMenuItem onSelect={() => setAppearance('dark')}>
          <Moon /> Dark
        </DropdownMenuItem>
        <DropdownMenuItem onSelect={() => setAppearance('system')}>
          <Monitor /> System
        </DropdownMenuItem>
      </DropdownMenuContent>
    </DropdownMenu>
  );
}
