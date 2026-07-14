import { api } from '@/lib/api';

export interface AppNotification {
  id: string;
  data: {
    title?: string;
    body?: string;
    url?: string | null;
    variant?: 'success' | 'warning' | 'danger' | 'info' | string;
  };
  read_at: string | null;
  created_at: string | null;
}

export interface PaginatedNotifications {
  data: AppNotification[];
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
  };
  unread_count: number;
}

export function fetchNotifications(page: number, perPage = 15): Promise<PaginatedNotifications> {
  return api.get<PaginatedNotifications>(`/notifications?page=${page}&per_page=${perPage}`);
}

export function markNotificationRead(id: string): Promise<{ message: string }> {
  return api.put<{ message: string }>(`/notifications/${id}/read`);
}

export function markAllNotificationsRead(): Promise<{ message: string }> {
  return api.post<{ message: string }>('/notifications/read-all');
}

export function deleteNotification(id: string): Promise<{ message: string }> {
  return api.delete<{ message: string }>(`/notifications/${id}`);
}
