import axios from 'axios';

class AuthService {
    constructor() {
        this.token = localStorage.getItem('auth_token');
        this.user = null;
        
        // Configurar axios por defecto
        this.setAuthToken(this.token);
    }

    /**
     * Configurar token de autorización en axios
     */
    setAuthToken(token) {
        if (token) {
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            localStorage.setItem('auth_token', token);
        } else {
            delete axios.defaults.headers.common['Authorization'];
            localStorage.removeItem('auth_token');
        }
        this.token = token;
    }

    /**
     * Registrar nuevo usuario
     */
    async register(userData) {
        try {
            const response = await axios.post('/api/register', userData);
            
            if (response.data.token) {
                this.setAuthToken(response.data.token);
                this.user = response.data.user;
            }
            
            return {
                success: true,
                data: response.data,
                user: response.data.user
            };
        } catch (error) {
            return {
                success: false,
                message: error.response?.data?.message || 'Error en el registro',
                errors: error.response?.data?.errors || {}
            };
        }
    }

    /**
     * Iniciar sesión
     */
    async login(credentials) {
        try {
            const response = await axios.post('/api/login', credentials);
            
            if (response.data.token) {
                this.setAuthToken(response.data.token);
                this.user = response.data.user;
            }
            
            return {
                success: true,
                data: response.data,
                user: response.data.user
            };
        } catch (error) {
            return {
                success: false,
                message: error.response?.data?.message || 'Error en el login',
                errors: error.response?.data?.errors || {}
            };
        }
    }

    /**
     * Cerrar sesión
     */
    async logout() {
        try {
            if (this.token) {
                await axios.post('/api/logout');
            }
        } catch (error) {
            console.error('Error al hacer logout:', error);
        } finally {
            this.setAuthToken(null);
            this.user = null;
        }
    }

    /**
     * Obtener usuario actual
     */
    async getCurrentUser() {
        if (!this.token) {
            return null;
        }

        try {
            const response = await axios.get('/api/user');
            this.user = response.data;
            return response.data;
        } catch (error) {
            // Si el token es inválido, limpiar la sesión
            this.logout();
            return null;
        }
    }

    /**
     * Verificar si el usuario está autenticado
     */
    isAuthenticated() {
        return !!this.token;
    }

    /**
     * Obtener token actual
     */
    getToken() {
        return this.token;
    }

    /**
     * Obtener usuario actual
     */
    getUser() {
        return this.user;
    }
}

// Crear instancia singleton
const authService = new AuthService();

export default authService;