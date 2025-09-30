import './bootstrap';
import { createApp } from 'vue';
import App from './App.vue';
import router from './router.js';
import axios from 'axios';

// Configurar axios globalmente
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';

// Configurar CSRF token para requests
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

// Configurar interceptores de Axios
import { setupAxiosInterceptors } from './interceptors/axios.js';

// Crear y montar la aplicación Vue
const app = createApp(App);
app.use(router);
app.config.globalProperties.$http = axios;

// Configurar interceptores después de tener el router disponible
setupAxiosInterceptors(router);

app.mount('#app');
