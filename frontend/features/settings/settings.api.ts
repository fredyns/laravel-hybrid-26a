import { api } from '@/lib/api';
import type { User } from '@/features/auth/auth.api';

export interface UpdateProfileInput {
  name: string;
  email: string;
}

export interface UpdateOwnPasswordInput {
  current_password: string;
  password: string;
  password_confirmation: string;
}

export function updateProfile(input: UpdateProfileInput): Promise<{ message: string; user: User }> {
  return api.put<{ message: string; user: User }>('/settings/profile', input);
}

export function updateOwnPassword(input: UpdateOwnPasswordInput): Promise<{ message: string }> {
  return api.put<{ message: string }>('/settings/password', input);
}

export function deleteAccount(password: string): Promise<{ message: string }> {
  return api.delete<{ message: string }>('/settings/account', { password });
}
