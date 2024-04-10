import './bootstrap';
import { createApp } from 'vue'
import app from './components/app.vue'
import router from './router/index.js'
import vuetify from '../plugins/vuetify.js'

createApp(app).use(router).use(vuetify).mount("#app") 