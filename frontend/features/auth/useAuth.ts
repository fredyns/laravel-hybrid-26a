import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import {
  fetchUser,
  login as loginRequest,
  logout as logoutRequest,
  register as registerRequest,
  type AuthResponse,
  type User,
} from '@/features/auth/auth.api';
import { clearToken, getToken, setToken } from '@/lib/auth-storage';

export const userQueryKey = ['user'] as const;

/**
 * Fetches the authenticated user. Only runs when a token is present so the
 * query stays idle for guests.
 */
export function useUser() {
  return useQuery<User>({
    queryKey: userQueryKey,
    queryFn: fetchUser,
    enabled: Boolean(getToken()),
    staleTime: 5 * 60_000,
  });
}

/** Whether a token currently exists in storage. */
export function isAuthenticated(): boolean {
  return Boolean(getToken());
}

/**
 * RBAC check against the permissions returned by GET /user. Returns false
 * while the user is still loading, so gated UI appears once known.
 */
export function useCan(): (permission: string) => boolean {
  const { data: user } = useUser();
  const permissions = user?.permissions ?? [];
  return (permission: string) => permissions.includes(permission);
}

function persistSession(queryClient: ReturnType<typeof useQueryClient>, data: AuthResponse): void {
  setToken(data.token);
  queryClient.setQueryData(userQueryKey, data.user);
  // The auth response lacks roles/permissions — refetch GET /user for RBAC data.
  void queryClient.invalidateQueries({ queryKey: userQueryKey });
}

export function useLogin() {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: loginRequest,
    onSuccess: (data) => persistSession(queryClient, data),
  });
}

export function useRegister() {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: registerRequest,
    onSuccess: (data) => persistSession(queryClient, data),
  });
}

/**
 * Optimistic logout: the token-revocation request is fired in the background
 * and the local session is cleared immediately, so callers can redirect
 * without waiting for the server. Errors are ignored — a failed server call
 * must still log the user out on the client.
 */
export function useLogout() {
  const queryClient = useQueryClient();
  return () => {
    void logoutRequest().catch(() => {});
    clearToken();
    queryClient.clear();
  };
}
