import axios from 'axios';

/**
 * Configurar interceptores de Axios
 */
export function setupAxiosInterceptors(router) {
    
    // Interceptor de request para añadir token automáticamente
    axios.interceptors.request.use(
        (config) => {
            // El token ya se configura en authService.setAuthToken()
            // pero este interceptor puede ser útil para logging o debug
            return config;
        },
        (error) => {
            return Promise.reject(error);
        }
    );

    // Interceptor de response para manejar errores de autenticación
    axios.interceptors.response.use(
        (response) => {
            return response;
        },
        (error) => {
            const { response } = error;
            
            if (response && response.status === 401) {
                // Token inválido o expirado
                // Limpiar localStorage y redirigir al login
                localStorage.removeItem('auth_token');
                delete axios.defaults.headers.common['Authorization'];
                
                // Redirigir al login solo si no estamos ya en una página de auth
                if (router.currentRoute.value.name !== 'login' && 
                    router.currentRoute.value.name !== 'register') {
                    router.push({ name: 'login' });
                }
            }
            
            return Promise.reject(error);
        }
    );
}