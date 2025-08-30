import { createApp } from 'vue'
import App from './App.vue'
import axios from './services/axios.js'
import './style.css'

// IMPORTA o Toastification e o CSS
import Toast, { POSITION } from 'vue-toastification'
import 'vue-toastification/dist/index.css'

const app = createApp(App)

// Deixa o axios global
app.config.globalProperties.$axios = axios

// Usa o toast com configurações (você pode mudar isso depois)
app.use(Toast, {
  position: POSITION.TOP_RIGHT,
  timeout: 3000,
  closeOnClick: true,
  pauseOnHover: true,
  draggable: true,
  draggablePercent: 0.6,
})

app.mount('#app')
