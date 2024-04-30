import './bootstrap';
import { createApp } from 'vue'
import app from './components/app.vue'
import router from './router/index.js'
import vuetify from '../plugins/vuetify.js'
import axios from 'axios'
import { registerLicense } from '@syncfusion/ej2-base';

registerLicense("Ngo9BigBOggjHTQxAR8/V1NBaF5cXmZCe0x3QHxbf1x0ZFZMYFtbQH5PMyBoS35RckVnWHpec3RSQmlZVkd0")
axios.defaults.withXSRFToken = true
createApp(app).use(router).use(vuetify).mount("#app") 