import { createApp } from 'vue'
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import '@/assets/styles/main.scss'
import App from './App.vue'
import i18n from '@/plugins/i18n'
import router from '@/router'
import store from '@/store'

const app = createApp(App)
app.use(ElementPlus)
app.use(i18n)
app.use(router)
app.use(store)
app.mount('#app')
