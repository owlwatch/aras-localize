import { fileURLToPath, URL } from 'node:url'
import path from 'node:path'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vuetify from 'vite-plugin-vuetify'

export default defineConfig({
  plugins: [vue(), vuetify({ autoImport: true })],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
    },
  },
  base: process.env.APP_ENV === 'development' ? '/' : './',
  build: {
    manifest: true,
    emptyOutDir: true,
    rollupOptions: {
      input: {
        main: path.resolve(__dirname, 'src/main.ts'),
        embed: path.resolve(__dirname, 'src/embed.ts'),
      },
    },
  },
  server: {
    port: 6240,
    origin: 'http://aras.local:6240',
    cors: true,
  },
})
