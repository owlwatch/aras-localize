import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import mkcert from 'vite-plugin-mkcert'

import path from 'path'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [vue(), mkcert()],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  },

  base: process.env.APP_ENV == 'development' 
    ? '/'
    : '/wp-content/plugins/' + (path.basename(path.dirname(__dirname))) + '/frontend' ,

  root: 'src',
  
  build: {
    outDir: '../dist',
    emptyOutDir: true,

    manifest: true,

    rollupOptions: {
      input: path.resolve(__dirname, 'src/main.ts')
    }
  },

  server: {
    strictPort: true,
    port: 5133
  }
})
