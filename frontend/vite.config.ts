import { defineConfig } from 'vite'
import { svelte } from '@sveltejs/vite-plugin-svelte'
import tailwindcss from '@tailwindcss/vite'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [svelte(), tailwindcss()],
  server: {
    host: '0.0.0.0',
    port: 5173,
    strictPort: true,
    cors: true,
    origin: 'http://192.168.50.126:5173',
    hmr: {
      protocol: 'ws',
      host: '192.168.50.126',
      port: 5173
    }
  },
  build: {
    // Output directory for production build
    outDir: '../backend/public/admin',
    // Generate sourcemaps for better debugging
    sourcemap: true,
    rollupOptions: {
      input: 'src/main.ts',
      output: {
        entryFileNames: 'assets/[name].js',
        chunkFileNames: 'assets/[name].js',
        assetFileNames: 'assets/[name].[ext]'
      }
    }
  }
})
