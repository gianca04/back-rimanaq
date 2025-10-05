/**
 * Utilidades de autenticación para el frontend
 * Maneja tokens de API y estado de autenticación
 */
class AuthHelper {
    /**
     * Obtiene el token de autenticación desde múltiples fuentes
     * @returns {string|null} Token de autenticación o null si no existe
     */
    static getToken() {
        // Buscar en localStorage (persistente)
        let token = localStorage.getItem('auth_token');
        if (token) return token;

        // Buscar en sessionStorage (sesión actual)
        token = sessionStorage.getItem('auth_token');
        if (token) return token;

        // Buscar en meta tag (para tokens de Laravel)
        const metaToken = document.querySelector('meta[name="api-token"]');
        if (metaToken) return metaToken.getAttribute('content');

        // Buscar en cookies (fallback)
        const cookieToken = this.getCookieValue('api_token');
        if (cookieToken) return cookieToken;

        return null;
    }

    /**
     * Establece el token de autenticación
     * @param {string} token - Token de autenticación
     * @param {boolean} persistent - Si el token debe persistir entre sesiones
     */
    static setToken(token, persistent = true) {
        if (persistent) {
            localStorage.setItem('auth_token', token);
        } else {
            sessionStorage.setItem('auth_token', token);
        }

        // También establecer en meta tag para consistencia
        let metaTag = document.querySelector('meta[name="api-token"]');
        if (!metaTag) {
            metaTag = document.createElement('meta');
            metaTag.setAttribute('name', 'api-token');
            document.head.appendChild(metaTag);
        }
        metaTag.setAttribute('content', token);
    }

    /**
     * Remueve el token de autenticación
     */
    static removeToken() {
        localStorage.removeItem('auth_token');
        sessionStorage.removeItem('auth_token');
        
        const metaTag = document.querySelector('meta[name="api-token"]');
        if (metaTag) {
            metaTag.remove();
        }
    }

    /**
     * Verifica si el usuario está autenticado
     * @returns {boolean} True si está autenticado
     */
    static isAuthenticated() {
        const token = this.getToken();
        return token !== null && token.length > 0;
    }

    /**
     * Obtiene headers estándar para peticiones autenticadas
     * @returns {Object} Headers de autenticación
     */
    static getAuthHeaders() {
        const headers = {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        };

        const token = this.getToken();
        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }

        // Agregar CSRF token si está disponible
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
            headers['X-CSRF-TOKEN'] = csrfToken;
        }

        return headers;
    }

    /**
     * Realiza una petición autenticada a la API
     * @param {string} url - URL de la petición
     * @param {Object} options - Opciones de fetch
     * @returns {Promise<Response>} Respuesta de la petición
     */
    static async authenticatedFetch(url, options = {}) {
        const authHeaders = this.getAuthHeaders();
        
        const requestOptions = {
            ...options,
            headers: {
                ...authHeaders,
                ...options.headers
            }
        };

        const response = await fetch(url, requestOptions);

        // Si la respuesta es 401, el token probablemente expiró
        if (response.status === 401) {
            this.handleUnauthorized();
        }

        return response;
    }

    /**
     * Maneja respuestas no autorizadas
     */
    static handleUnauthorized() {
        this.removeToken();
        
        // Mostrar mensaje al usuario
        if (window.showAlert) {
            window.showAlert('Tu sesión ha expirado. Por favor, inicia sesión nuevamente.', 'warning');
        }

        // Redirigir al login después de un breve delay
        setTimeout(() => {
            window.location.href = '/login';
        }, 2000);
    }

    /**
     * Obtiene el valor de una cookie por nombre
     * @param {string} name - Nombre de la cookie
     * @returns {string|null} Valor de la cookie o null
     */
    static getCookieValue(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) {
            return parts.pop().split(';').shift();
        }
        return null;
    }

    /**
     * Valida el formato del token JWT (básico)
     * @param {string} token - Token a validar
     * @returns {boolean} True si el formato es válido
     */
    static isValidTokenFormat(token) {
        if (!token || typeof token !== 'string') return false;
        
        // Verificar que tenga el formato básico de un JWT (tres partes separadas por puntos)
        const parts = token.split('.');
        return parts.length === 3;
    }

    /**
     * Decodifica el payload de un JWT (sin verificación de firma)
     * @param {string} token - Token JWT
     * @returns {Object|null} Payload decodificado o null si hay error
     */
    static decodeJWTPayload(token) {
        try {
            if (!this.isValidTokenFormat(token)) return null;
            
            const payload = token.split('.')[1];
            const decoded = atob(payload.replace(/-/g, '+').replace(/_/g, '/'));
            return JSON.parse(decoded);
        } catch (error) {
            console.error('Error decodificando JWT:', error);
            return null;
        }
    }

    /**
     * Verifica si el token ha expirado
     * @param {string} token - Token a verificar
     * @returns {boolean} True si el token ha expirado
     */
    static isTokenExpired(token) {
        const payload = this.decodeJWTPayload(token);
        if (!payload || !payload.exp) return false;
        
        const now = Math.floor(Date.now() / 1000);
        return now >= payload.exp;
    }

    /**
     * Obtiene información del usuario desde el token
     * @param {string} token - Token JWT
     * @returns {Object|null} Información del usuario o null
     */
    static getUserFromToken(token) {
        const payload = this.decodeJWTPayload(token);
        if (!payload) return null;
        
        return {
            id: payload.sub || payload.user_id,
            email: payload.email,
            name: payload.name,
            exp: payload.exp,
            iat: payload.iat
        };
    }
}

// Hacer disponible globalmente
window.AuthHelper = AuthHelper;