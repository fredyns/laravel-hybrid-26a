import { useState } from 'react';
import { Link, useLocation } from '@tanstack/react-router';
import { ChevronDown } from 'lucide-react';
import { AppLogo, AppLogoMark } from '@/components/app-logo';
import { UserMenu } from '@/features/app-shell/UserMenu';
import { authGroup, dashboardItem, notificationsItem, type NavItem } from '@/features/app-shell/nav';
import { useCan } from '@/features/auth/useAuth';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { cn } from '@/lib/utils';

function SidebarLink({ item, collapsed = false }: { item: NavItem; collapsed?: boolean }) {
  const { pathname } = useLocation();
  const current = pathname.startsWith(item.match);
  const Icon = item.icon;

  return (
    <Link
      to={item.to}
      title={collapsed ? item.label : undefined}
      className={cn(
        'flex items-center rounded-lg text-sm transition-colors',
        collapsed ? 'size-10 justify-center' : 'gap-2.5 px-3 py-2',
        current
          ? 'bg-gradient-to-r from-indigo-500 to-fuchsia-500 font-medium text-white shadow-md shadow-indigo-950/50'
          : 'text-indigo-100/70 hover:bg-white/10 hover:text-white',
      )}
    >
      <Icon className="size-4 shrink-0" />
      {!collapsed && item.label}
    </Link>
  );
}

/**
 * Sidebar content shared by the desktop sidebar (sidebar mode) and the mobile
 * drawer (both modes). Renders on the dark ink surface; `collapsed` switches
 * to an icon-only rail where the Auth group becomes a flyout menu.
 */
export function SidebarContent({ collapsed = false }: { collapsed?: boolean }) {
  const { pathname } = useLocation();
  const can = useCan();
  const visibleAuthItems = authGroup.items.filter((item) => !item.permission || can(item.permission));
  const groupActive = visibleAuthItems.some((item) => pathname.startsWith(item.match));
  // Follow the active route unless the user toggled the group manually.
  const [manualOpen, setManualOpen] = useState<boolean | null>(null);
  const authOpen = manualOpen ?? groupActive;
  const GroupIcon = authGroup.icon;

  return (
    <div className={cn('flex h-full flex-col gap-4', collapsed ? 'items-center p-3' : 'p-4')}>
      {collapsed ? (
        <Link to="/dashboard" title="Dashboard" className="py-2">
          <AppLogoMark />
        </Link>
      ) : (
        <Link to="/dashboard" className="px-1 py-2">
          <AppLogo className="text-white" />
        </Link>
      )}

      <nav
        className={cn(
          'flex min-h-0 flex-1 flex-col gap-1 overflow-y-auto',
          collapsed && 'items-center overflow-x-hidden',
        )}
      >
        {!collapsed && (
          <div className="px-3 py-2 text-[10px] font-semibold uppercase tracking-widest text-indigo-200/50">
            Platform
          </div>
        )}

        <SidebarLink item={dashboardItem} collapsed={collapsed} />

        {visibleAuthItems.length > 0 &&
          (collapsed ? (
            <DropdownMenu>
              <DropdownMenuTrigger
                title={authGroup.label}
                className={cn(
                  'flex size-10 cursor-pointer items-center justify-center rounded-lg transition-colors',
                  groupActive
                    ? 'bg-gradient-to-r from-indigo-500 to-fuchsia-500 text-white shadow-md shadow-indigo-950/50'
                    : 'text-indigo-100/70 hover:bg-white/10 hover:text-white',
                )}
              >
                <GroupIcon className="size-4" />
              </DropdownMenuTrigger>
              <DropdownMenuContent side="right" align="start">
                {visibleAuthItems.map((item) => (
                  <DropdownMenuItem key={item.to} asChild>
                    <Link to={item.to}>
                      <item.icon />
                      {item.label}
                    </Link>
                  </DropdownMenuItem>
                ))}
              </DropdownMenuContent>
            </DropdownMenu>
          ) : (
            <>
              <button
                type="button"
                onClick={() => setManualOpen(!authOpen)}
                className="flex w-full cursor-pointer items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-indigo-100/70 transition-colors hover:bg-white/10 hover:text-white"
              >
                <GroupIcon className="size-4 shrink-0" />
                <span className="flex-1 text-start">{authGroup.label}</span>
                <ChevronDown className={cn('size-4 transition-transform', !authOpen && '-rotate-90')} />
              </button>

              {authOpen && (
                <div className="ms-3 flex flex-col gap-1 border-s border-white/15 ps-3">
                  {visibleAuthItems.map((item) => (
                    <SidebarLink key={item.to} item={item} />
                  ))}
                </div>
              )}
            </>
          ))}

        <div className={cn('mt-auto', !collapsed && 'w-full')}>
          <SidebarLink item={notificationsItem} collapsed={collapsed} />
        </div>
      </nav>

      <UserMenu variant="sidebar" collapsed={collapsed} />
    </div>
  );
}

/** Deep indigo gradient surface shared by the desktop sidebar and the mobile drawer. */
export const sidebarSurface =
  'border-e border-white/10 bg-gradient-to-b from-indigo-950 via-purple-950 to-zinc-950 text-indigo-100';
