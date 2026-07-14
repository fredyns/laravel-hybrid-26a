import { api } from '@/lib/api';
import type { LoginInput, RegisterInput } from '@/features/auth/auth.schemas';

/** Authenticated user, mirrors the `User` schema in the OpenAPI spec. */
export interface User {
  id: string;
  name: string;
  email: string;
  email_verified_at: string | null;
  created_at: string | null;
  updated_at: string | null;
  two_factor_confirmed_at?: string | null;
  /** API-guard role names; present on GET /user only. */
  roles?: string[];
  /** API-guard permission names; present on GET /user only. */
  permissions?: string[];
}

export interface AuthResponse {
  message: string;
  user: User;
  token: string;
}

export interface MessageResponse {
  message: string;
}

export function login(input: LoginInput): Promise<AuthResponse> {
  return api.post<AuthResponse>('/auth/login', input);
}

export function register(input: RegisterInput): Promise<AuthResponse> {
  return api.post<AuthResponse>('/auth/register', input);
}

export function logout(): Promise<MessageResponse> {
  return api.post<MessageResponse>('/auth/logout');
}

export function fetchUser(): Promise<User> {
  return api.get<User>('/user');
}
