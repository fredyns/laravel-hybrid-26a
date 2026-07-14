import {
  createHashHistory,
  createRootRoute,
  createRoute,
  createRouter,
  Outlet,
  redirect,
} from '@tanstack/react-router';
import { isAuthenticated } from '@/features/auth/useAuth';
import { AuthLayout } from '@/features/auth/AuthLayout';
import { LoginPage } from '@/features/auth/LoginPage';
import { RegisterPage } from '@/features/auth/RegisterPage';
import { AppShell } from '@/features/app-shell/AppShell';
import { LandingPage } from '@/features/landing/LandingPage';
import { DashboardPage } from '@/features/dashboard/DashboardPage';
import { UsersPage } from '@/features/users/UsersPage';
import { UserShowPage } from '@/features/users/UserShowPage';
import { RolesPage } from '@/features/roles/RolesPage';
import { RoleShowPage } from '@/features/roles/RoleShowPage';
import { NotificationsPage } from '@/features/notifications/NotificationsPage';
import { SettingsLayout } from '@/features/settings/SettingsLayout';
import { ProfilePage } from '@/features/settings/ProfilePage';
import { SecurityPage } from '@/features/settings/SecurityPage';
import { AppearancePage } from '@/features/settings/AppearancePage';

const rootRoute = createRootRoute({
  component: () => <Outlet />,
});

/** Public landing page. */
const indexRoute = createRoute({
  getParentRoute: () => rootRoute,
  path: '/',
  component: LandingPage,
});

/** Pathless layout for unauthenticated screens (split auth layout). */
const guestRoute = createRoute({
  getParentRoute: () => rootRoute,
  id: 'guest',
  beforeLoad: () => {
    if (isAuthenticated()) {
      throw redirect({ to: '/dashboard' });
    }
  },
  component: AuthLayout,
});

const loginRoute = createRoute({
  getParentRoute: () => guestRoute,
  path: '/login',
  component: LoginPage,
});

const registerRoute = createRoute({
  getParentRoute: () => guestRoute,
  path: '/register',
  component: RegisterPage,
});

/** Pathless layout guarding authenticated screens, wrapped in the hybrid shell. */
const authRoute = createRoute({
  getParentRoute: () => rootRoute,
  id: 'authenticated',
  beforeLoad: () => {
    if (!isAuthenticated()) {
      throw redirect({ to: '/login' });
    }
  },
  component: AppShell,
});

const dashboardRoute = createRoute({
  getParentRoute: () => authRoute,
  path: '/dashboard',
  component: DashboardPage,
});

const usersRoute = createRoute({
  getParentRoute: () => authRoute,
  path: '/users',
  component: UsersPage,
});

const userShowRoute = createRoute({
  getParentRoute: () => authRoute,
  path: '/users/$userId',
  component: UserShowPage,
});

const rolesRoute = createRoute({
  getParentRoute: () => authRoute,
  path: '/roles',
  component: RolesPage,
});

const roleShowRoute = createRoute({
  getParentRoute: () => authRoute,
  path: '/roles/$roleId',
  component: RoleShowPage,
});

const notificationsRoute = createRoute({
  getParentRoute: () => authRoute,
  path: '/notifications',
  component: NotificationsPage,
});

const settingsRoute = createRoute({
  getParentRoute: () => authRoute,
  path: '/settings',
  component: SettingsLayout,
});

const settingsIndexRoute = createRoute({
  getParentRoute: () => settingsRoute,
  path: '/',
  beforeLoad: () => {
    throw redirect({ to: '/settings/profile' });
  },
});

const settingsProfileRoute = createRoute({
  getParentRoute: () => settingsRoute,
  path: '/profile',
  component: ProfilePage,
});

const settingsSecurityRoute = createRoute({
  getParentRoute: () => settingsRoute,
  path: '/security',
  component: SecurityPage,
});

const settingsAppearanceRoute = createRoute({
  getParentRoute: () => settingsRoute,
  path: '/appearance',
  component: AppearancePage,
});

const routeTree = rootRoute.addChildren([
  indexRoute,
  guestRoute.addChildren([loginRoute, registerRoute]),
  authRoute.addChildren([
    dashboardRoute,
    usersRoute,
    userShowRoute,
    rolesRoute,
    roleShowRoute,
    notificationsRoute,
    settingsRoute.addChildren([
      settingsIndexRoute,
      settingsProfileRoute,
      settingsSecurityRoute,
      settingsAppearanceRoute,
    ]),
  ]),
]);

export const router = createRouter({
  routeTree,
  history: createHashHistory(),
  defaultPreload: 'intent',
});

declare module '@tanstack/react-router' {
  interface Register {
    router: typeof router;
  }
}
