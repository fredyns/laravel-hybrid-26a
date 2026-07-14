/**
 * Application shell component providing the main layout wrapper for authenticated routes.
 *
 * This component renders either a sidebar-based or header-based navigation layout depending on
 * the user's layout mode preference (switchable from the user menu). It includes:
 * - Navigation: dark ink sidebar that collapses to an icon rail, or a top bar with inline nav
 * - Breadcrumb navigation
 * - Notification bell with unread count badge
 * - Theme switcher
 * - User menu
 * - Mobile drawer for responsive navigation
 *
 * @usage
 * Used as the component for the authenticated route in router.tsx:
 * ```tsx
 * const authRoute = createRoute({
 *   getParentRoute: () => rootRoute,
 *   id: 'authenticated',
 *   beforeLoad: () => {
 *     if (!isAuthenticated()) {
 *       throw redirect({ to: '/login' });
 *     }
 *   },
 *   component: AppShell,
 * });
 * ```
 *
 * The shell automatically switches between SidebarShell and HeaderShell based on the
 * layout mode returned by useLayoutMode().
 */
import {useState} from 'react';
import {Link, Outlet, useLocation} from '@tanstack/react-router';
import {Bell, ChevronDown, Menu, PanelLeft, X} from 'lucide-react';
import {AppLogo} from '@/components/app-logo';
import {Breadcrumbs} from '@/features/app-shell/Breadcrumbs';
import {SidebarContent, sidebarSurface} from '@/features/app-shell/Sidebar';
import {ThemeDropdown} from '@/features/app-shell/ThemeDropdown';
import {UserMenu} from '@/features/app-shell/UserMenu';
import {authGroup, breadcrumbsFor, dashboardItem} from '@/features/app-shell/nav';
import {useCan} from '@/features/auth/useAuth';
import {useNotificationsList} from '@/features/notifications/useNotifications';
import {useLayoutMode} from '@/lib/layout-mode';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {cn} from '@/lib/utils';

/**
 * Glassy surface for horizontal bars, crowned by a thin indigo→fuchsia→amber
 * accent line along the top edge.
 */
const headerSurface =
    'relative border-b border-border/60 bg-background/70 backdrop-blur-xl ' +
    'before:absolute before:inset-x-0 before:top-0 before:h-0.5 before:bg-gradient-to-r before:from-indigo-500 before:via-fuchsia-500 before:to-amber-400';

/** Soft color wash behind page content, keeping it readable in both themes. */
const contentWash =
    'bg-gradient-to-br from-indigo-50/70 via-transparent to-fuchsia-50/60 dark:from-indigo-950/25 dark:via-transparent dark:to-fuchsia-950/20';

/** Icon button used for header actions and nav toggles. */
const headerIconButton =
    'flex size-8 cursor-pointer items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-accent hover:text-foreground';

function NotificationBell() {
    const {data} = useNotificationsList(1);
    const unreadCount = data?.unread_count ?? 0;

    return (
        <Link to="/notifications" aria-label="Notifications" className={cn('relative', headerIconButton)}>
            <Bell className="size-4"/>
            {unreadCount > 0 && (
                <span
                    className="absolute -end-0.5 -top-0.5 flex min-w-4 items-center justify-center rounded-full bg-gradient-to-br from-fuchsia-500 to-rose-500 px-1 text-[10px] font-semibold leading-4 text-white">
          {unreadCount > 99 ? '99+' : unreadCount}
        </span>
            )}
        </Link>
    );
}

function MobileDrawer({open, onClose}: { open: boolean; onClose: () => void }) {
    if (!open) {
        return null;
    }
    return (
        <div className="fixed inset-0 z-50 lg:hidden">
            <div
                className="absolute inset-0 bg-zinc-950/60 backdrop-blur-sm animate-in fade-in-0"
                onClick={onClose}
            />
            <div
                className={cn(
                    'absolute inset-y-0 start-0 w-72 duration-200 animate-in slide-in-from-left',
                    sidebarSurface,
                )}
            >
                <button
                    type="button"
                    onClick={onClose}
                    aria-label="Close menu"
                    className="absolute end-2 top-4 flex size-8 cursor-pointer items-center justify-center rounded-md text-zinc-400 hover:bg-white/10 hover:text-white"
                >
                    <X className="size-4"/>
                </button>
                <SidebarContent/>
            </div>
        </div>
    );
}

/** Sidebar navigation mode: dark ink sidebar collapsible to an icon rail. */
function SidebarShell() {
    const [drawerOpen, setDrawerOpen] = useState(false);
    const [collapsed, setCollapsed] = useState(false);

    return (
        <div className={cn('flex min-h-dvh', contentWash)}>
            <aside
                className={cn(
                    'sticky top-0 hidden h-dvh shrink-0 transition-[width] duration-200 lg:block',
                    collapsed ? 'w-17' : 'w-64',
                    sidebarSurface,
                )}
            >
                <SidebarContent collapsed={collapsed}/>
            </aside>

            <MobileDrawer open={drawerOpen} onClose={() => setDrawerOpen(false)}/>

            <div className="flex min-w-0 flex-1 flex-col">
                <header className={cn('sticky top-0 z-10 flex h-14 items-center gap-3 px-4 lg:px-6', headerSurface)}>
                    <button
                        type="button"
                        aria-label="Open menu"
                        onClick={() => setDrawerOpen(true)}
                        className={cn(headerIconButton, 'lg:hidden')}
                    >
                        <Menu className="size-4"/>
                    </button>
                    <button
                        type="button"
                        aria-label="Toggle sidebar"
                        onClick={() => setCollapsed((value) => !value)}
                        className={cn(headerIconButton, 'hidden lg:flex')}
                    >
                        <PanelLeft className="size-4"/>
                    </button>

                    <Breadcrumbs/>

                    <div className="ms-auto flex items-center gap-0.5">
                        <ThemeDropdown/>
                        <NotificationBell/>
                        <div className="lg:hidden">
                            <UserMenu variant="header"/>
                        </div>
                    </div>
                </header>

                <main className="flex-1 p-6 lg:p-8">
                    <Outlet/>
                </main>
            </div>
        </div>
    );
}

/** Top-bar navigation mode: translucent bar with inline pill navigation. */
function HeaderShell() {
    const [drawerOpen, setDrawerOpen] = useState(false);
    const {pathname} = useLocation();
    const can = useCan();
    const visibleAuthItems = authGroup.items.filter((item) => !item.permission || can(item.permission));
    const crumbs = breadcrumbsFor(pathname);

    return (
        <div className={cn('flex min-h-dvh flex-col', contentWash)}>
            <header className={cn('sticky top-0 z-10', headerSurface)}>
                <div className="mx-auto flex h-14 max-w-7xl items-center gap-4 px-4 lg:px-8">
                    <button
                        type="button"
                        aria-label="Toggle menu"
                        onClick={() => setDrawerOpen(true)}
                        className={cn(headerIconButton, 'lg:hidden')}
                    >
                        <Menu className="size-4"/>
                    </button>

                    <Link to="/dashboard">
                        <AppLogo/>
                    </Link>

                    <nav className="hidden items-center gap-1 lg:flex">
                        {/* Dashboard pill */}
                        <Link
                            to="/dashboard"
                            className={cn(
                                'flex items-center gap-2 rounded-full px-3 py-1.5 text-sm transition-colors',
                                pathname.startsWith('/dashboard')
                                    ? 'bg-gradient-to-r from-indigo-500 to-fuchsia-500 font-medium text-white shadow-md shadow-indigo-500/30'
                                    : 'text-muted-foreground hover:bg-accent hover:text-foreground',
                            )}
                        >
                            <dashboardItem.icon className="size-4"/>
                            {dashboardItem.label}
                        </Link>

                        {/* Auth dropdown */}
                        {visibleAuthItems.length > 0 && (
                            <DropdownMenu>
                                <DropdownMenuTrigger
                                    className={cn(
                                        'flex cursor-pointer items-center gap-2 rounded-full px-3 py-1.5 text-sm transition-colors',
                                        visibleAuthItems.some((item) => pathname.startsWith(item.match))
                                            ? 'bg-gradient-to-r from-indigo-500 to-fuchsia-500 font-medium text-white shadow-md shadow-indigo-500/30'
                                            : 'text-muted-foreground hover:bg-accent hover:text-foreground',
                                    )}
                                >
                                    <authGroup.icon className="size-4"/>
                                    {authGroup.label}
                                    <ChevronDown className="size-3.5"/>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="start">
                                    {visibleAuthItems.map((item) => (
                                        <DropdownMenuItem key={item.to} asChild>
                                            <Link to={item.to}>
                                                <item.icon/>
                                                {item.label}
                                            </Link>
                                        </DropdownMenuItem>
                                    ))}
                                </DropdownMenuContent>
                            </DropdownMenu>
                        )}
                    </nav>

                    <div className="ms-auto flex items-center gap-0.5">
                        <ThemeDropdown/>
                        <NotificationBell/>
                        <div className="ms-1">
                            <UserMenu variant="header"/>
                        </div>
                    </div>
                </div>
            </header>

            <MobileDrawer open={drawerOpen} onClose={() => setDrawerOpen(false)}/>

            {crumbs.length > 0 && (
                <div className="mx-auto w-full max-w-7xl px-4 pt-3 opacity-80 lg:px-8">
                    <Breadcrumbs className="text-xs"/>
                </div>
            )}

            <main className="mx-auto w-full max-w-7xl flex-1 px-4 py-8 lg:px-8">
                <Outlet/>
            </main>
        </div>
    );
}

export function AppShell() {
    const layoutMode = useLayoutMode();
    return layoutMode === 'header' ? <HeaderShell/> : <SidebarShell/>;
}
