import { useAuth } from '../composables/useAuth.js';

/**
 * Guardia de navegación para rutas que requieren autenticación
 */
export function requireAuth(to, from, next) {
    const { isAuthenticated } = useAuth();
    
    if (isAuthenticated.value) {
        next();
    } else {
        next({ name: 'login' });
    }
}

/**
 * Guardia de navegación para rutas de invitados (login, registro)
 * Redirige a la página principal si ya está autenticado
 */
export function requireGuest(to, from, next) {
    const { isAuthenticated } = useAuth();
    
    if (isAuthenticated.value) {
        next({ name: 'courses' });
    } else {
        next();
    }
}