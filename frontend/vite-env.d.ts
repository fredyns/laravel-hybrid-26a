/// <reference types="vite/client" />

interface ImportMetaEnv {
  /** Optional API base override; defaults to same-origin /api. */
  readonly VITE_API_URL?: string;
}

interface ImportMeta {
  readonly env: ImportMetaEnv;
}
