import { useSyncExternalStore } from 'react';

export type Appearance = 'light' | 'dark' | 'system';

const STORAGE_KEY = 'appearance';
const listeners = new Set<() => void>();

const systemDark = window.matchMedia('(prefers-color-scheme: dark)');

function readAppearance(): Appearance {
  const value = localStorage.getItem(STORAGE_KEY);
  return value === 'light' || value === 'dark' ? value : 'system';
}

function isDark(appearance: Appearance): boolean {
  return appearance === 'dark' || (appearance === 'system' && systemDark.matches);
}

/** Stamp the `.dark` class on <html> to match the stored preference. */
function applyAppearance(): void {
  document.documentElement.classList.toggle('dark', isDark(readAppearance()));
}

export function setAppearance(value: Appearance): void {
  localStorage.setItem(STORAGE_KEY, value);
  applyAppearance();
  listeners.forEach((notify) => notify());
}

function subscribe(notify: () => void): () => void {
  listeners.add(notify);
  return () => listeners.delete(notify);
}

/** Reactive access to the appearance preference. */
export function useAppearance(): Appearance {
  return useSyncExternalStore(subscribe, readAppearance);
}

// Apply on module load and follow OS changes while in "system" mode.
applyAppearance();
systemDark.addEventListener('change', applyAppearance);
