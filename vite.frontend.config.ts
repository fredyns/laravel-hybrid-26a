import { fileURLToPath, URL } from 'node:url';
import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';

/**
 * Build config for the React SPA in frontend/.
 *
 * Emits a static public/index.html plus hashed chunks in public/spa/, which
 * nginx serves directly at "/" (index.html takes priority over index.php).
 * Kept separate from vite.config.js because laravel-vite-plugin empties
 * public/build on every blade-asset build.
 */
export default defineConfig({
  root: 'frontend',
  envDir: fileURLToPath(new URL('./', import.meta.url)),
  plugins: [react(), tailwindcss()],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./frontend', import.meta.url)),
    },
  },
  build: {
    outDir: '../public',
    assetsDir: 'spa',
    emptyOutDir: false,
  },
  server: {
    port: 5173,
  },
});
