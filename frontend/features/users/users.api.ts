import { api } from '@/lib/api';
import type { User } from '@/features/auth/auth.api';

export interface UsersListParams {
  search?: string;
  sort_by?: 'name' | 'email' | 'email_verified_at' | 'created_at' | 'updated_at';
  sort_dir?: 'asc' | 'desc';
  per_page?: number;
  page?: number;
}

/** Laravel paginator envelope produced by UserResource::collection(paginate). */
export interface PaginatedUsers {
  data: User[];
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
  };
}

export interface CreateUserInput {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
}

export interface UpdateUserInput {
  name: string;
  email: string;
}

export interface ChangePasswordInput {
  password: string;
  password_confirmation: string;
}

interface UserMessageResponse {
  message: string;
  user: User;
}

export function fetchUsers(params: UsersListParams): Promise<PaginatedUsers> {
  const query = new URLSearchParams();
  Object.entries(params).forEach(([key, value]) => {
    if (value !== undefined && value !== '') {
      query.set(key, String(value));
    }
  });
  const queryString = query.toString();
  return api.get<PaginatedUsers>(`/users${queryString ? `?${queryString}` : ''}`);
}

export function fetchUserById(id: string): Promise<{ data: User }> {
  return api.get<{ data: User }>(`/users/${id}`);
}

export function createUser(input: CreateUserInput): Promise<UserMessageResponse> {
  return api.post<UserMessageResponse>('/users', input);
}

export function updateUser(id: string, input: UpdateUserInput): Promise<UserMessageResponse> {
  return api.put<UserMessageResponse>(`/users/${id}`, input);
}

export function deleteUser(id: string): Promise<{ message: string }> {
  return api.delete<{ message: string }>(`/users/${id}`);
}

export function changeUserPassword(id: string, input: ChangePasswordInput): Promise<{ message: string }> {
  return api.put<{ message: string }>(`/users/${id}/password`, input);
}
