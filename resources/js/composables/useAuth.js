import { ref, computed } from 'vue';
import authService from '../services/auth.js';

// Estado global reactivo
const user = ref(null);
const isLoggedIn = ref(false);
const loading = ref(false);

export function useAuth() {
    
    /**
     * Inicializar autenticación al cargar la aplicación
     */
    const initAuth = async () => {
        loading.value = true;
        
        try {
            if (authService.isAuthenticated()) {
                const currentUser = await authService.getCurrentUser();
                if (currentUser) {
                    user.value = currentUser;
                    isLoggedIn.value = true;
                }
            }
        } catch (error) {
            console.error('Error al inicializar autenticación:', error);
        } finally {
            loading.value = false;
        }
    };

    /**
     * Registrar nuevo usuario
     */
    const register = async (userData) => {
        loading.value = true;
        
        try {
            const result = await authService.register(userData);
            
            if (result.success) {
                user.value = result.user;
                isLoggedIn.value = true;
            }
            
            return result;
        } catch (error) {
            console.error('Error en registro:', error);
            return {
                success: false,
                message: 'Error inesperado durante el registro'
            };
        } finally {
            loading.value = false;
        }
    };

    /**
     * Iniciar sesión
     */
    const login = async (credentials) => {
        loading.value = true;
        
        try {
            const result = await authService.login(credentials);
            
            if (result.success) {
                user.value = result.user;
                isLoggedIn.value = true;
            }
            
            return result;
        } catch (error) {
            console.error('Error en login:', error);
            return {
                success: false,
                message: 'Error inesperado durante el login'
            };
        } finally {
            loading.value = false;
        }
    };

    /**
     * Cerrar sesión
     */
    const logout = async () => {
        loading.value = true;
        
        try {
            await authService.logout();
            user.value = null;
            isLoggedIn.value = false;
        } catch (error) {
            console.error('Error en logout:', error);
        } finally {
            loading.value = false;
        }
    };

    /**
     * Refrescar datos del usuario
     */
    const refreshUser = async () => {
        if (!isLoggedIn.value) return;
        
        try {
            const currentUser = await authService.getCurrentUser();
            if (currentUser) {
                user.value = currentUser;
            }
        } catch (error) {
            console.error('Error al refrescar usuario:', error);
            // Si hay error, cerrar sesión
            await logout();
        }
    };

    // Computed properties
    const userName = computed(() => user.value?.name || '');
    const userEmail = computed(() => user.value?.email || '');
    const isAuthenticated = computed(() => isLoggedIn.value && !!user.value);

    return {
        // Estado
        user: computed(() => user.value),
        isLoggedIn: computed(() => isLoggedIn.value),
        loading: computed(() => loading.value),
        
        // Computed
        userName,
        userEmail,
        isAuthenticated,
        
        // Métodos
        initAuth,
        register,
        login,
        logout,
        refreshUser
    };
}