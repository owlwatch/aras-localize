import { createApp } from 'vue'
import '@mdi/font/css/materialdesignicons.css'

import App from './App.vue'
import { vuetify } from './plugins/vuetify'
import './styles/main.scss'

createApp(App).use(vuetify).mount('#aras-support-matrix-app')
