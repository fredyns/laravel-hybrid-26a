import { useState } from 'react';
import { toast } from 'sonner';
import { Bell, Check, CheckCheck, ChevronLeft, ChevronRight, Loader2, Trash2 } from 'lucide-react';
import {
  useDeleteNotification,
  useMarkAllNotificationsRead,
  useMarkNotificationRead,
  useNotificationsList,
} from '@/features/notifications/useNotifications';
import { getErrorMessage } from '@/lib/api';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

function variantBadge(variant: string | undefined): 'emerald' | 'amber' | 'indigo' | 'default' {
  switch (variant) {
    case 'success':
      return 'emerald';
    case 'warning':
    case 'danger':
      return 'amber';
    case 'info':
      return 'indigo';
    default:
      return 'default';
  }
}

function formatTime(value: string | null): string {
  return value ? new Date(value).toLocaleString() : '—';
}

/** Notifications index backed by GET /api/notifications. */
export function NotificationsPage() {
  const [page, setPage] = useState(1);
  const { data, isPending, isError, error } = useNotificationsList(page);
  const markRead = useMarkNotificationRead();
  const markAllRead = useMarkAllNotificationsRead();
  const remove = useDeleteNotification();

  const notifications = data?.data ?? [];
  const meta = data?.meta;
  const unreadCount = data?.unread_count ?? 0;

  async function run(action: Promise<{ message: string }>) {
    try {
      const result = await action;
      toast.success(result.message);
    } catch (err) {
      toast.error(getErrorMessage(err));
    }
  }

  return (
    <div className="space-y-6">
      <div className="flex items-start justify-between gap-4">
        <div>
          <h1 className="text-2xl font-semibold tracking-tight">Notifications</h1>
          <p className="mt-1 text-sm text-muted-foreground">View and manage your notifications.</p>
        </div>
        {unreadCount > 0 && (
          <Button variant="outline" onClick={() => void run(markAllRead.mutateAsync())}>
            <CheckCheck /> Mark all read
          </Button>
        )}
      </div>

      {isError ? (
        <p className="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/50 dark:bg-red-950/50 dark:text-red-300">
          {getErrorMessage(error, 'Unable to load notifications.')}
        </p>
      ) : isPending ? (
        <div className="flex justify-center py-16">
          <Loader2 className="size-6 animate-spin text-zinc-400" />
        </div>
      ) : notifications.length === 0 ? (
        <>
          <Table>
            <TableHeader>
              <TableRow className="hover:bg-transparent dark:hover:bg-transparent">
                <TableHead>Status</TableHead>
                <TableHead>Notification</TableHead>
                <TableHead>Time</TableHead>
                <TableHead className="text-end">Actions</TableHead>
              </TableRow>
            </TableHeader>
          </Table>
          <div className="flex flex-col items-center gap-2 border-b border-zinc-100 py-12 text-center dark:border-zinc-700/60">
            <Bell className="size-6 text-zinc-300 dark:text-zinc-600" />
            <p className="text-sm text-muted-foreground">No notifications yet.</p>
          </div>
        </>
      ) : (
        <>
          <Table>
            <TableHeader>
              <TableRow className="hover:bg-transparent dark:hover:bg-transparent">
                <TableHead>Status</TableHead>
                <TableHead>Notification</TableHead>
                <TableHead>Time</TableHead>
                <TableHead className="text-end">Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {notifications.map((notification) => (
                <TableRow key={notification.id}>
                  <TableCell>
                    {notification.read_at ? (
                      <Badge>Read</Badge>
                    ) : (
                      <Badge variant={variantBadge(notification.data.variant)}>Unread</Badge>
                    )}
                  </TableCell>
                  <TableCell>
                    <p className="font-medium">{notification.data.title ?? 'Notification'}</p>
                    {notification.data.body && (
                      <p className="mt-0.5 text-muted-foreground">{notification.data.body}</p>
                    )}
                  </TableCell>
                  <TableCell className="whitespace-nowrap text-muted-foreground">
                    {formatTime(notification.created_at)}
                  </TableCell>
                  <TableCell className="text-end">
                    <div className="flex justify-end gap-1">
                      {!notification.read_at && (
                        <Button
                          variant="ghost"
                          size="icon"
                          className="size-8"
                          aria-label="Mark as read"
                          onClick={() => void run(markRead.mutateAsync(notification.id))}
                        >
                          <Check />
                        </Button>
                      )}
                      <Button
                        variant="ghost"
                        size="icon"
                        className="size-8 text-red-500 hover:text-red-600"
                        aria-label="Delete"
                        onClick={() => void run(remove.mutateAsync(notification.id))}
                      >
                        <Trash2 />
                      </Button>
                    </div>
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>

          {meta && meta.last_page > 1 && (
            <div className="flex items-center justify-between gap-4">
              <p className="text-sm text-muted-foreground">
                Showing {meta.from ?? 0} to {meta.to ?? 0} of {meta.total} results
              </p>
              <div className="flex items-center gap-1">
                <Button
                  variant="outline"
                  size="icon"
                  className="size-8"
                  aria-label="Previous page"
                  disabled={meta.current_page <= 1}
                  onClick={() => setPage((value) => value - 1)}
                >
                  <ChevronLeft />
                </Button>
                <span className="px-2 text-sm text-muted-foreground">
                  Page {meta.current_page} of {meta.last_page}
                </span>
                <Button
                  variant="outline"
                  size="icon"
                  className="size-8"
                  aria-label="Next page"
                  disabled={meta.current_page >= meta.last_page}
                  onClick={() => setPage((value) => value + 1)}
                >
                  <ChevronRight />
                </Button>
              </div>
            </div>
          )}
        </>
      )}
    </div>
  );
}
