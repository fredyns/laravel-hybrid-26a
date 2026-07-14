import { useState } from 'react';
import { useNavigate } from '@tanstack/react-router';
import { toast } from 'sonner';
import { ChevronsUpDown, ChevronDown, Cog, LogOut, Menu, PanelLeft } from 'lucide-react';
import { useLogout, useUser } from '@/features/auth/useAuth';
import { setLayoutMode, useLayoutMode } from '@/lib/layout-mode';
import { Avatar } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogTitle } from '@/components/ui/dialog';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

/**
 * User dropdown with settings, layout-mode toggle and logout. `variant`
 * controls the trigger style: sidebar profile row (on the dark ink surface,
 * avatar-only when `collapsed`) vs compact header avatar.
 */
export function UserMenu({ variant, collapsed = false }: { variant: 'sidebar' | 'header'; collapsed?: boolean }) {
  const navigate = useNavigate();
  const { data: user } = useUser();
  const layoutMode = useLayoutMode();
  const logout = useLogout();
  const [confirmLogoutOpen, setConfirmLogoutOpen] = useState(false);

  if (!user) {
    return null;
  }

  /**
   * Optimistic logout: clear the session and go straight to the landing
   * page; the server-side token revocation completes in the background.
   */
  function confirmLogout() {
    logout();
    setConfirmLogoutOpen(false);
    toast.success('Signed out');
    void navigate({ to: '/' });
  }

  return (
    <>
      <DropdownMenu>
        {variant === 'sidebar' ? (
          collapsed ? (
            <DropdownMenuTrigger
              title={user.name}
              className="flex cursor-pointer items-center justify-center rounded-lg p-1 hover:bg-white/10"
            >
              <Avatar name={user.name} />
            </DropdownMenuTrigger>
          ) : (
            <DropdownMenuTrigger className="flex w-full cursor-pointer items-center gap-2 rounded-lg p-2 text-start text-indigo-50 transition-colors hover:bg-white/10">
              <Avatar name={user.name} />
              <span className="flex-1 truncate text-sm font-medium">{user.name}</span>
              <ChevronsUpDown className="size-4 text-indigo-200/60" />
            </DropdownMenuTrigger>
          )
        ) : (
          <DropdownMenuTrigger className="flex cursor-pointer items-center gap-1 rounded-md p-1 transition-colors hover:bg-accent">
            <Avatar name={user.name} />
            <ChevronDown className="size-3.5 text-zinc-400" />
          </DropdownMenuTrigger>
        )}

        <DropdownMenuContent
          side={variant === 'sidebar' && collapsed ? 'right' : 'bottom'}
          align={variant === 'sidebar' ? 'start' : 'end'}
          className="min-w-56"
        >
          <div className="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
            <Avatar name={user.name} />
            <div className="grid flex-1 leading-tight">
              <span className="truncate font-semibold">{user.name}</span>
              <span className="truncate text-xs text-muted-foreground">{user.email}</span>
            </div>
          </div>

          <DropdownMenuSeparator />

          <DropdownMenuItem onSelect={() => navigate({ to: '/settings/profile' })}>
            <Cog /> Settings
          </DropdownMenuItem>

          <DropdownMenuSeparator />

          {layoutMode === 'sidebar' ? (
            <DropdownMenuItem onSelect={() => setLayoutMode('header')}>
              <Menu /> Top bar layout
            </DropdownMenuItem>
          ) : (
            <DropdownMenuItem onSelect={() => setLayoutMode('sidebar')}>
              <PanelLeft /> Sidebar layout
            </DropdownMenuItem>
          )}

          <DropdownMenuSeparator />

          <DropdownMenuItem onSelect={() => setConfirmLogoutOpen(true)}>
            <LogOut /> Log out
          </DropdownMenuItem>
        </DropdownMenuContent>
      </DropdownMenu>

      <Dialog open={confirmLogoutOpen} onOpenChange={setConfirmLogoutOpen}>
        <DialogContent>
          <DialogTitle>Log out</DialogTitle>
          <DialogDescription>Are you sure you want to log out?</DialogDescription>

          <div className="mt-6 flex justify-end gap-2">
            <Button type="button" variant="outline" onClick={() => setConfirmLogoutOpen(false)}>
              Cancel
            </Button>
            <Button type="button" onClick={confirmLogout}>
              <LogOut /> Log out
            </Button>
          </div>
        </DialogContent>
      </Dialog>
    </>
  );
}
