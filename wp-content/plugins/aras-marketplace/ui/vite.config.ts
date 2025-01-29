import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'

let relativeURL = __dirname.replace(/.*\/wp\-content/, '/wp-content');

import path from 'path';

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    
  ],

  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  },

  base: process.env.APP_ENV == 'development' 
    ? '/'
    : relativeURL+'/dist/' ,

  build: {
    manifest: true,
    emptyOutDir: true,
    rollupOptions: {
      input: path.resolve(__dirname, 'src/main.ts')
    }
  },

  server: {
    port : 6238,
    cors : true
  },

  css: {
    preprocessorOptions: {
      scss: {
        api: 'modern-compiler' // or "modern"
      }
    }
  },
})
