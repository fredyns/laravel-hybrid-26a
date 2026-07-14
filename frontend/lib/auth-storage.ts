const TOKEN_KEY = 'auth_token';

/**
 * Small wrapper around localStorage for the Sanctum bearer token so that token
 * access is centralised and easy to swap (e.g. to cookies) later.
 */
export function getToken(): string | null {
  return localStorage.getItem(TOKEN_KEY);
}

export function setToken(token: string): void {
  localStorage.setItem(TOKEN_KEY, token);
}

export function clearToken(): void {
  localStorage.removeItem(TOKEN_KEY);
}
