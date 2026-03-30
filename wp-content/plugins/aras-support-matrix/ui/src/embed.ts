import { createApp } from 'vue'
import '@mdi/font/css/materialdesignicons.css'

import EmbeddedApp from './EmbeddedApp.vue'
import { getConfig } from './composables/api'
import { vuetify } from './plugins/vuetify'
import './styles/main.scss'

function mount(selector?: string) {
  const config = getConfig()
  const target = document.querySelector(selector ?? config.mountSelector ?? '#aras-support-matrix-embed')

  if (!target) {
    console.warn('Aras Support Matrix embed target not found.')
    return
  }

  createApp(EmbeddedApp).use(vuetify).mount(target)
}

declare global {
  interface Window {
    ArasSupportMatrixEmbed?: {
      mount: (selector?: string) => void
    }
  }
}

window.ArasSupportMatrixEmbed = { mount }

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => mount())
} else {
  mount()
}
