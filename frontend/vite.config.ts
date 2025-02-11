import { fileURLToPath, URL } from 'node:url'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'

// Load environment variables

export default defineConfig({
  plugins: [vue(), vueDevTools()],
  server: {
    port: 8080, // Make sure it matches your frontend port
  },
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
    },
  },
})
