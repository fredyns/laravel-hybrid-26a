import { api } from '@/lib/api';

export type GuardName = 'web' | 'sanctum';

export interface Role {
  id: string;
  name: string;
  guard_name: GuardName;
  protected: boolean;
  permissions_count?: number;
  users_count?: number;
  /** Permission names; present when the role is loaded individually. */
  permissions?: string[];
  created_at: string | null;
  updated_at: string | null;
}

export interface RolesListParams {
  search?: string;
  guard?: GuardName;
  per_page?: number;
  page?: number;
}

export interface PaginatedRoles {
  data: Role[];
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
  };
}

export interface RoleInput {
  name: string;
  guard: GuardName;
  permissions: string[];
}

interface RoleMessageResponse {
  message: string;
  role: Role;
}

/** Permission names grouped by resource prefix, e.g. { users: [...], rbac: [...] }. */
export type PermissionGroups = Record<string, string[]>;

export function fetchRoles(params: RolesListParams): Promise<PaginatedRoles> {
  const query = new URLSearchParams();
  Object.entries(params).forEach(([key, value]) => {
    if (value !== undefined && value !== '') {
      query.set(key, String(value));
    }
  });
  const queryString = query.toString();
  return api.get<PaginatedRoles>(`/roles${queryString ? `?${queryString}` : ''}`);
}

export function fetchRoleById(id: string): Promise<{ data: Role }> {
  return api.get<{ data: Role }>(`/roles/${id}`);
}

export function createRole(input: RoleInput): Promise<RoleMessageResponse> {
  return api.post<RoleMessageResponse>('/roles', input);
}

export function updateRole(id: string, input: RoleInput): Promise<RoleMessageResponse> {
  return api.put<RoleMessageResponse>(`/roles/${id}`, input);
}

export function deleteRole(id: string): Promise<{ message: string }> {
  return api.delete<{ message: string }>(`/roles/${id}`);
}

export function fetchPermissionGroups(guard: GuardName): Promise<{ data: PermissionGroups }> {
  return api.get<{ data: PermissionGroups }>(`/permissions?guard=${guard}`);
}
