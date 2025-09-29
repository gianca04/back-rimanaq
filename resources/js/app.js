import './bootstrap';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import App from './App.vue';
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

// Importar componentes
import CoursesList from './components/CoursesList.vue';
import CourseForm from './components/CourseForm.vue';

// Configurar rutas
const routes = [
    { 
        path: '/', 
        name: 'courses', 
        component: CoursesList 
    },
    { 
        path: '/courses/create', 
        name: 'courses.create', 
        component: CourseForm 
    },
    { 
        path: '/courses/:id/edit', 
        name: 'courses.edit', 
        component: CourseForm,
        props: true 
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

// Crear y montar la aplicación Vue
const app = createApp(App);
app.use(router);
app.config.globalProperties.$http = axios;
app.mount('#app');
