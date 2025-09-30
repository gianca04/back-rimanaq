import { createRouter, createWebHistory } from 'vue-router';

// Importar componentes
import CoursesList from './components/CoursesList.vue';
import CourseForm from './components/CourseForm.vue';
import CourseDetail from './components/CourseDetail.vue';
import LessonsList from './components/LessonsList.vue';
import LessonForm from './components/LessonForm.vue';
import AllLessonsList from './components/AllLessonsList.vue';
import LoginForm from './components/LoginForm.vue';
import RegisterForm from './components/RegisterForm.vue';

// Importar middlewares
import { requireAuth, requireGuest } from './middleware/auth.js';

// Configurar rutas
const routes = [
    { 
        path: '/', 
        name: 'courses', 
        component: CoursesList,
        beforeEnter: requireAuth
    },
    { 
        path: '/courses/create', 
        name: 'courses.create', 
        component: CourseForm,
        beforeEnter: requireAuth
    },
    { 
        path: '/courses/:id/edit', 
        name: 'courses.edit', 
        component: CourseForm,
        props: true,
        beforeEnter: requireAuth
    },
    { 
        path: '/courses/:id', 
        name: 'courses.show', 
        component: CourseDetail,
        props: true,
        beforeEnter: requireAuth
    },
    {
        path: '/courses/:courseId/lessons',
        name: 'lessons.index',
        component: LessonsList,
        beforeEnter: requireAuth
    },
    {
        path: '/courses/:courseId/lessons/create',
        name: 'lessons.create',
        component: LessonForm,
        beforeEnter: requireAuth
    },
    {
        path: '/courses/:courseId/lessons/:id/edit',
        name: 'lessons.edit',
        component: LessonForm,
        props: true,
        beforeEnter: requireAuth
    },
    // Rutas globales de lecciones
    {
        path: '/lessons',
        name: 'all-lessons',
        component: AllLessonsList,
        beforeEnter: requireAuth
    },
    {
        path: '/lessons/create',
        name: 'all-lessons.create',
        component: LessonForm,
        beforeEnter: requireAuth
    },
    {
        path: '/lessons/:id/edit',
        name: 'all-lessons.edit',
        component: LessonForm,
        props: true,
        beforeEnter: requireAuth
    },
    {
        path: '/login',
        name: 'login',
        component: LoginForm,
        beforeEnter: requireGuest
    },
    {
        path: '/register',
        name: 'register',
        component: RegisterForm,
        beforeEnter: requireGuest
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;