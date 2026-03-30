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
          primary: '#CC2027',
          secondary: '#1E212B',
          accent: '#D49623',
          'brand-red': '#CC2027',
          'brand-yellow': '#D49623',
          'brand-dark-gray': '#4F4F4F',
          'brand-light-gray': '#CFCFCF',
          'brand-black': '#1E212B',
          surface: '#ffffff',
          background: '#ffffff',
          success: '#2e7d32',
          info: '#4F4F4F',
          error: '#CC2027',
          warning: '#D49623',
        },
      },
    },
  },
})
