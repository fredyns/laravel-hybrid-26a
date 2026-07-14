import { useSyncExternalStore } from 'react';

/** Hybrid navigation: full sidebar vs top bar, mirroring the Livewire layout_mode cookie. */
export type LayoutMode = 'sidebar' | 'header';

const STORAGE_KEY = 'layout_mode';
const listeners = new Set<() => void>();

function readLayoutMode(): LayoutMode {
  return localStorage.getItem(STORAGE_KEY) === 'header' ? 'header' : 'sidebar';
}

export function setLayoutMode(mode: LayoutMode): void {
  localStorage.setItem(STORAGE_KEY, mode);
  listeners.forEach((notify) => notify());
}

function subscribe(notify: () => void): () => void {
  listeners.add(notify);
  return () => listeners.delete(notify);
}

export function useLayoutMode(): LayoutMode {
  return useSyncExternalStore(subscribe, readLayoutMode);
}
