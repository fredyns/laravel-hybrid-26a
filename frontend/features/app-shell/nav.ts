import { Bell, Home, Lock, Shield, Users } from 'lucide-react';
import type { LucideIcon } from 'lucide-react';

export interface NavItem {
  label: string;
  to: string;
  icon: LucideIcon;
  /** Path prefix that marks this item as current. */
  match: string;
  /** Permission required to see this item; undefined = always visible. */
  permission?: string;
}

export const dashboardItem: NavItem = { label: 'Dashboard', to: '/dashboard', icon: Home, match: '/dashboard' };

/** "Auth" group, shown as an expandable sidebar group / navbar dropdown. */
export const authGroup = {
  label: 'Auth',
  icon: Lock,
  items: [
    { label: 'Users', to: '/users', icon: Users, match: '/users', permission: 'users.index' },
    { label: 'Roles', to: '/roles', icon: Shield, match: '/roles', permission: 'rbac.index' },
  ] satisfies NavItem[],
};

export const notificationsItem: NavItem = {
  label: 'Notifications',
  to: '/notifications',
  icon: Bell,
  match: '/notifications',
};

/** Breadcrumb trail for the current path, mirroring layouts/app.blade.php. */
export function breadcrumbsFor(pathname: string): { label: string; to?: string }[] {
  if (pathname.startsWith('/users')) {
    return [{ label: 'Users', to: '/users' }];
  }
  if (pathname.startsWith('/roles')) {
    return [{ label: 'Roles', to: '/roles' }];
  }
  if (pathname.startsWith('/settings')) {
    return [{ label: 'Settings' }];
  }
  return [];
}
