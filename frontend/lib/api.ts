import { clearToken, getToken } from '@/lib/auth-storage';

/** Shape of a Laravel 422 validation error payload. */
export interface ValidationErrorResponse {
  message: string;
  errors: Record<string, string[]>;
}

/** Shape of a generic Laravel error payload (401, etc.). */
export interface MessageErrorResponse {
  message: string;
}

/** Error thrown for non-2xx API responses, carrying the parsed JSON payload. */
export class ApiError extends Error {
  readonly status: number;
  readonly payload: unknown;

  constructor(status: number, payload: unknown) {
    super(
      typeof payload === 'object' && payload !== null && 'message' in payload
        ? String((payload as MessageErrorResponse).message)
        : `Request failed with status ${status}`,
    );
    this.name = 'ApiError';
    this.status = status;
    this.payload = payload;
  }
}

/** Endpoints whose 401 responses must not trigger the global session reset. */
const AUTH_ENDPOINTS = ['/auth/login', '/auth/register', '/auth/logout'];

async function request<T>(path: string, options: RequestInit = {}): Promise<T> {
  const headers = new Headers({
    Accept: 'application/json',
    ...(options.body ? { 'Content-Type': 'application/json' } : {}),
  });

  const token = getToken();
  if (token) {
    headers.set('Authorization', `Bearer ${token}`);
  }

  const response = await fetch(`${import.meta.env.VITE_API_URL}${path}`, { ...options, headers });
  const payload: unknown = response.status === 204 ? null : await response.json().catch(() => null);

  if (!response.ok) {
    // Globally handle expired/invalid sessions: if an authenticated request
    // comes back 401 we drop the stored token and bounce to the login screen.
    // Failed login/register attempts are left for the calling form to handle.
    const isAuthAttempt = AUTH_ENDPOINTS.some((endpoint) => path.startsWith(endpoint));
    if (response.status === 401 && !isAuthAttempt) {
      clearToken();
      if (!window.location.hash.startsWith('#/login')) {
        window.location.hash = '#/login';
      }
    }
    throw new ApiError(response.status, payload);
  }

  return payload as T;
}

export const api = {
  get: <T>(path: string) => request<T>(path),
  post: <T>(path: string, body?: unknown) =>
    request<T>(path, { method: 'POST', body: body === undefined ? undefined : JSON.stringify(body) }),
  put: <T>(path: string, body?: unknown) =>
    request<T>(path, { method: 'PUT', body: body === undefined ? undefined : JSON.stringify(body) }),
  delete: <T>(path: string, body?: unknown) =>
    request<T>(path, { method: 'DELETE', body: body === undefined ? undefined : JSON.stringify(body) }),
};

/** Type guard for Laravel 422 validation responses. */
export function isValidationError(
  error: unknown,
): error is ApiError & { payload: ValidationErrorResponse } {
  return (
    error instanceof ApiError &&
    error.status === 422 &&
    typeof error.payload === 'object' &&
    error.payload !== null &&
    typeof (error.payload as ValidationErrorResponse).errors === 'object'
  );
}

/**
 * Extract a human-readable message from any API error, falling back to a
 * sensible default when the server response has no message.
 */
export function getErrorMessage(error: unknown, fallback = 'Something went wrong'): string {
  if (error instanceof ApiError) {
    const data = error.payload as MessageErrorResponse | null;
    if (data?.message) {
      return data.message;
    }
  }
  return fallback;
}

/**
 * Build a form-facing error message: for 422 responses it joins the individual
 * field validation messages; otherwise it returns the top-level message.
 */
export function getFormErrorMessage(error: unknown, fallback = 'Something went wrong'): string {
  if (isValidationError(error)) {
    const messages = Object.values(error.payload.errors).flat().filter(Boolean);
    if (messages.length > 0) {
      return messages.join(' ');
    }
  }
  return getErrorMessage(error, fallback);
}
