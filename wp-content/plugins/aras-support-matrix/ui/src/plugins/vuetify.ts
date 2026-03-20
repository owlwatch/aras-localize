import 'vuetify/styles'

import { createVuetify } from 'vuetify'
import { aliases, mdi } from 'vuetify/iconsets/mdi'

export const vuetify = createVuetify({
  defaults: {
    VDialog: {
      contentClass: 'aras-support-matrix-dialog',
    },
    VMenu: {
      contentClass: 'aras-support-matrix-overlay',
    },
  },
  icons: {
    defaultSet: 'mdi',
    aliases,
    sets: {
      mdi,
    },
  },
  theme: {
    defaultTheme: 'aras',
    themes: {
      aras: {
        dark: false,
        colors: {
          primary: '#0c5ff4',
          secondary: '#07294d',
          surface: '#ffffff',
          background: '#ffffff',
          success: '#2e7d32',
          info: '#1976d2',
          error: '#c62828',
          warning: '#ef6c00',
        },
      },
    },
  },
})
