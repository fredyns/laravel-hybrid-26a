import { keepPreviousData, useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import {
  deleteNotification,
  fetchNotifications,
  markAllNotificationsRead,
  markNotificationRead,
} from '@/features/notifications/notifications.api';

const NOTIFICATIONS_KEY = 'notifications';

export function useNotificationsList(page: number, perPage?: number) {
  return useQuery({
    queryKey: [NOTIFICATIONS_KEY, { page, perPage }],
    queryFn: () => fetchNotifications(page, perPage),
    placeholderData: keepPreviousData,
    // Keep the bell badge reasonably fresh without websockets.
    refetchInterval: 60_000,
  });
}

function useInvalidateNotifications() {
  const queryClient = useQueryClient();
  return () => queryClient.invalidateQueries({ queryKey: [NOTIFICATIONS_KEY] });
}

export function useMarkNotificationRead() {
  const invalidate = useInvalidateNotifications();
  return useMutation({
    mutationFn: (id: string) => markNotificationRead(id),
    onSuccess: invalidate,
  });
}

export function useMarkAllNotificationsRead() {
  const invalidate = useInvalidateNotifications();
  return useMutation({
    mutationFn: markAllNotificationsRead,
    onSuccess: invalidate,
  });
}

export function useDeleteNotification() {
  const invalidate = useInvalidateNotifications();
  return useMutation({
    mutationFn: (id: string) => deleteNotification(id),
    onSuccess: invalidate,
  });
}
