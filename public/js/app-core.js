/**
 * Core App - Funcionalidad común para todas las páginas del dashboard
 */
window.App = (function() {
    'use strict';

    // Configuración de la aplicación
    const Config = {
        api: {
            baseUrl: '/api',
            endpoints: {
                lessons: '/api/lessons',
                courses: '/api/courses',
                gestures: '/api/gestures',
                progress: '/api/progress',
                logout: '/api/logout'
            }
        },
        ui: {
            pageSize: 15,
            animationDuration: 300
        }
    };

    // Estado de la aplicación
    const State = {
        isLoading: false,
        currentUser: null,
        authToken: null
    };

    // Utilidades
    const Utils = {
        showLoading: function() {
            State.isLoading = true;
            $('#loadingOverlay').fadeIn(Config.ui.animationDuration);
        },

        hideLoading: function() {
            State.isLoading = false;
            $('#loadingOverlay').fadeOut(Config.ui.animationDuration);
        },

        showAlert: function(message, type = 'success', duration = 5000) {
            const alertId = 'alert_' + Date.now();
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" id="${alertId}" role="alert">
                    <i class="fas fa-${this.getAlertIcon(type)} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            $('#alertsContainer').prepend(alertHtml);
            
            if (duration > 0) {
                setTimeout(() => {
                    $(`#${alertId}`).alert('close');
                }, duration);
            }
        },

        getAlertIcon: function(type) {
            const icons = {
                'success': 'check-circle',
                'danger': 'exclamation-triangle',
                'warning': 'exclamation-circle',
                'info': 'info-circle'
            };
            return icons[type] || 'info-circle';
        },

        formatDifficulty: function(difficulty) {
            const badges = {
                'fácil': '<span class="badge bg-success">Fácil</span>',
                'intermedio': '<span class="badge bg-warning text-dark">Intermedio</span>',
                'difícil': '<span class="badge bg-danger">Difícil</span>'
            };
            return badges[difficulty] || difficulty;
        },

        formatDuration: function(minutes) {
            if (minutes < 60) {
                return `${minutes} min`;
            }
            
            const hours = Math.floor(minutes / 60);
            const remainingMinutes = minutes % 60;
            
            if (remainingMinutes === 0) {
                return hours === 1 ? "1 hora" : `${hours} horas`;
            }
            
            return hours === 1 
                ? `1 hora ${remainingMinutes} min`
                : `${hours} horas ${remainingMinutes} min`;
        },

        parseFormData: function(postData) {
            let formData = {};
            
            if (typeof postData === 'string') {
                // Parsear la cadena URL-encoded a objeto
                const params = new URLSearchParams(postData);
                for (let [key, value] of params.entries()) {
                    formData[key] = decodeURIComponent(value);
                }
            } else {
                formData = { ...postData };
            }
            
            return formData;
        },

        validateRequired: function(data, requiredFields) {
            const errors = [];
            
            requiredFields.forEach(field => {
                if (!data[field] || (typeof data[field] === 'string' && data[field].trim() === '')) {
                    errors.push(`El campo ${field} es requerido`);
                }
            });
            
            return errors;
        }
    };

    // API
    const API = {
        request: function(url, options = {}) {
            const defaultOptions = {
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Authorization': 'Bearer ' + State.authToken,
                    'Accept': 'application/json'
                }
            };

            return $.ajax({
                url: url,
                dataType: 'json',
                ...defaultOptions,
                ...options
            });
        },

        handleError: function(xhr, context = '') {
            console.error(`API Error ${context}:`, xhr);
            
            let message = 'Error interno del servidor';
            
            if (xhr.responseJSON) {
                if (xhr.responseJSON.errors) {
                    const errorsList = Object.entries(xhr.responseJSON.errors).map(([field, messages]) => {
                        return `${field}: ${Array.isArray(messages) ? messages.join(', ') : messages}`;
                    });
                    message = 'Errores de validación:\n' + errorsList.join('\n');
                } else if (xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
            } else {
                message = `Error ${xhr.status}: ${xhr.statusText}`;
            }
            
            Utils.showAlert(message, 'danger');
            return message;
        }
    };

    // Autenticación
    const Auth = {
        init: function() {
            State.authToken = localStorage.getItem('auth_token');
            State.currentUser = JSON.parse(localStorage.getItem('user_data') || 'null');
        },

        check: function() {
            if (!State.authToken) {
                Utils.showAlert('Sesión expirada. Redirigiendo al login...', 'warning');
                setTimeout(() => {
                    window.location.href = '/';
                }, 2000);
                return false;
            }
            return true;
        },

        logout: function() {
            if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
                Utils.showLoading();
                
                API.request(Config.api.endpoints.logout, {
                    method: 'POST'
                })
                .always(() => {
                    localStorage.removeItem('auth_token');
                    localStorage.removeItem('user_data');
                    Utils.showAlert('Sesión cerrada exitosamente', 'success');
                    setTimeout(() => {
                        window.location.href = '/';
                    }, 1000);
                })
                .fail(() => {
                    // En caso de error, igual redirigir
                    window.location.href = '/';
                })
                .always(() => {
                    Utils.hideLoading();
                });
            }
        }
    };

    // Configuración global de AJAX
    function setupAjax() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Authorization': 'Bearer ' + State.authToken
            }
        });
    }

    // Inicialización
    function init() {
        Auth.init();
        
        if (!Auth.check()) {
            return;
        }
        
        setupAjax();
        
        // Event listeners globales
        $(document).ajaxStart(function() {
            if (!State.isLoading) {
                Utils.showLoading();
            }
        });

        $(document).ajaxStop(function() {
            Utils.hideLoading();
        });
        
        console.log('App Core initialized');
    }

    // API pública
    return {
        // Configuración
        Config: Config,
        State: State,
        
        // Módulos
        Utils: Utils,
        API: API,
        Auth: Auth,
        
        // Métodos principales
        init: init,
        logout: Auth.logout,
        
        // Helpers para compatibilidad
        showLoading: Utils.showLoading,
        hideLoading: Utils.hideLoading,
        showAlert: Utils.showAlert
    };
})();

// Auto-inicializar cuando el DOM esté listo
$(document).ready(function() {
    App.init();
});