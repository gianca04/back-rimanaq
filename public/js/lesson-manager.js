/**
 * Gestor de lecciones - Maneja CRUD de lecciones
 */
class LessonManager {
    constructor() {
        this.apiBaseUrl = '/api';
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        this.authToken = localStorage.getItem('auth_token');
    }

    /**
     * Obtiene los headers para las peticiones API
     */
    getHeaders() {
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };

        if (this.authToken) {
            headers['Authorization'] = `Bearer ${this.authToken}`;
        }

        if (this.csrfToken) {
            headers['X-CSRF-TOKEN'] = this.csrfToken;
        }

        return headers;
    }

    /**
     * Obtiene una lección específica por ID
     * @param {number} lessonId - ID de la lección
     * @returns {Promise<Object>} - Datos de la lección
     */
    async getLessonById(lessonId) {
        try {
            const response = await fetch(`${this.apiBaseUrl}/lessons/${lessonId}`, {
                method: 'GET',
                headers: this.getHeaders()
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Error al obtener la lección');
            }

            if (!result.success) {
                throw new Error(result.message || 'Error en la respuesta del servidor');
            }

            return result.data;
        } catch (error) {
            console.error('Error al obtener lección:', error);
            throw error;
        }
    }

    /**
     * Obtiene todas las lecciones
     * @returns {Promise<Array>} - Lista de lecciones
     */
    async getAllLessons() {
        try {
            const response = await fetch(`${this.apiBaseUrl}/lessons`, {
                method: 'GET',
                headers: this.getHeaders()
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Error al obtener las lecciones');
            }

            if (!result.success) {
                throw new Error(result.message || 'Error en la respuesta del servidor');
            }

            return result.data;
        } catch (error) {
            console.error('Error al obtener lecciones:', error);
            throw error;
        }
    }

    /**
     * Crea una nueva lección
     * @param {Object} lessonData - Datos de la lección
     * @returns {Promise<Object>} - Lección creada
     */
    async createLesson(lessonData) {
        try {
            const response = await fetch(`${this.apiBaseUrl}/lessons`, {
                method: 'POST',
                headers: this.getHeaders(),
                body: JSON.stringify(lessonData)
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Error al crear la lección');
            }

            if (!result.success) {
                throw new Error(result.message || 'Error en la respuesta del servidor');
            }

            return result.data;
        } catch (error) {
            console.error('Error al crear lección:', error);
            throw error;
        }
    }

    /**
     * Actualiza una lección existente
     * @param {number} lessonId - ID de la lección
     * @param {Object} lessonData - Datos actualizados
     * @returns {Promise<Object>} - Lección actualizada
     */
    async updateLesson(lessonId, lessonData) {
        try {
            const response = await fetch(`${this.apiBaseUrl}/lessons/${lessonId}`, {
                method: 'PUT',
                headers: this.getHeaders(),
                body: JSON.stringify(lessonData)
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Error al actualizar la lección');
            }

            if (!result.success) {
                throw new Error(result.message || 'Error en la respuesta del servidor');
            }

            return result.data;
        } catch (error) {
            console.error('Error al actualizar lección:', error);
            throw error;
        }
    }

    /**
     * Elimina una lección
     * @param {number} lessonId - ID de la lección
     * @returns {Promise<boolean>} - Resultado de la eliminación
     */
    async deleteLesson(lessonId) {
        try {
            const response = await fetch(`${this.apiBaseUrl}/lessons/${lessonId}`, {
                method: 'DELETE',
                headers: this.getHeaders()
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Error al eliminar la lección');
            }

            if (!result.success) {
                throw new Error(result.message || 'Error en la respuesta del servidor');
            }

            return true;
        } catch (error) {
            console.error('Error al eliminar lección:', error);
            throw error;
        }
    }

    /**
     * Limpia ambos formularios (lección y contenido)
     */
    clearAllForms() {
        try {
            // Limpiar formulario de lección
            if (typeof window.clearLessonForm === 'function') {
                window.clearLessonForm();
            } else {
                console.warn('Función clearLessonForm no encontrada');
                // Fallback: limpiar manualmente
                this.clearLessonFormManually();
            }

            // Limpiar formulario de contenido
            if (typeof window.clearContentForm === 'function') {
                window.clearContentForm();
            } else {
                console.warn('Función clearContentForm no encontrada');
            }

            console.log('Ambos formularios limpiados correctamente');
        } catch (error) {
            console.error('Error al limpiar formularios:', error);
        }
    }

    /**
     * Limpia manualmente el formulario de lección (fallback)
     */
    clearLessonFormManually() {
        const fields = [
            'course_id', 'name', 'level_number', 
            'description', 'difficulty', 'time_minutes'
        ];

        fields.forEach(field => {
            const element = document.getElementById(field);
            if (element) {
                element.value = '';
                element.classList.remove('is-invalid');
            }
        });

        // Limpiar contenido
        const contentField = document.getElementById('content');
        if (contentField) {
            contentField.value = '[]';
        }

        // Limpiar mensajes de error
        document.querySelectorAll('.invalid-feedback').forEach(el => {
            el.textContent = '';
        });

        // Remover ID de lección del formulario
        const form = document.getElementById('lessonForm');
        if (form) {
            delete form.dataset.lessonId;
        }
    }

    /**
     * Prepara el formulario para crear una nueva lección
     */
    prepareForNewLesson() {
        try {
            // Limpiar ambos formularios
            this.clearAllForms();

            // Mostrar mensaje de éxito
            if (typeof window.showToast === 'function') {
                window.showToast('Formulario preparado para nueva lección', 'info');
            }

            console.log('Formulario preparado para nueva lección');
        } catch (error) {
            console.error('Error al preparar formulario para nueva lección:', error);
            
            if (typeof window.showToast === 'function') {
                window.showToast('Error al preparar el formulario', 'error');
            }
        }
    }

    /**
     * Carga los datos de una lección en el formulario de edición
     * @param {number} lessonId - ID de la lección a editar
     */
    async loadLessonForEdit(lessonId) {
        try {
            // Mostrar loading
            this.showLoading('Cargando datos de la lección...');

            // PRIMERO: Limpiar ambos formularios
            this.clearAllForms();

            // Obtener datos de la lección
            const lessonData = await this.getLessonById(lessonId);

            // Marcar el formulario como edición
            const form = document.getElementById('lessonForm');
            if (form) {
                form.dataset.lessonId = lessonId;
            }

            // Cargar datos en el formulario si existe la función
            if (typeof window.loadLessonData === 'function') {
                window.loadLessonData(lessonData);
            } else {
                console.warn('Función loadLessonData no encontrada');
                // Fallback: cargar datos manualmente
                this.loadLessonDataManually(lessonData);
            }

            this.hideLoading();

            // Mostrar mensaje de éxito
            if (typeof window.showToast === 'function') {
                window.showToast('Datos de la lección cargados correctamente', 'success');
            }

            return lessonData;
        } catch (error) {
            this.hideLoading();
            
            // Mostrar error
            if (typeof window.showToast === 'function') {
                window.showToast(`Error al cargar la lección: ${error.message}`, 'error');
            } else {
                alert(`Error al cargar la lección: ${error.message}`);
            }
            
            throw error;
        }
    }

    /**
     * Carga los datos de la lección manualmente en el formulario
     * @param {Object} lessonData - Datos de la lección
     */
    loadLessonDataManually(lessonData) {
        // Cargar datos básicos
        const fields = [
            'course_id', 'name', 'level_number', 
            'description', 'difficulty', 'time_minutes'
        ];

        fields.forEach(field => {
            const element = document.getElementById(field);
            if (element && lessonData[field] !== undefined) {
                element.value = lessonData[field];
            }
        });

        // Cargar contenido
        const contentField = document.getElementById('content');
        if (contentField && lessonData.content) {
            let contentValue;
            if (typeof lessonData.content === 'string') {
                contentValue = lessonData.content;
            } else {
                contentValue = JSON.stringify(lessonData.content);
            }
            
            contentField.value = contentValue;

            // Recargar el formulario de contenido si existe
            setTimeout(() => {
                if (typeof window.jsonToContentArray === 'function') {
                    window.jsonToContentArray(contentValue);
                }
            }, 100);
        }
    }

    /**
     * Maneja el envío del formulario de lección
     * @param {Event} event - Evento del formulario
     * @param {number|null} lessonId - ID de la lección (null para crear nueva)
     */
    async handleFormSubmit(event, lessonId = null) {
        event.preventDefault();

        try {
            // Mostrar loading
            this.showLoading(lessonId ? 'Actualizando lección...' : 'Creando lección...');

            // Obtener datos del formulario
            let formData;
            if (typeof window.getLessonFormData === 'function') {
                formData = window.getLessonFormData();
            } else {
                formData = this.getFormDataManually();
            }

            // Validar datos básicos
            if (!this.validateFormData(formData)) {
                this.hideLoading();
                return;
            }

            // Crear o actualizar lección
            let result;
            if (lessonId) {
                result = await this.updateLesson(lessonId, formData);
            } else {
                result = await this.createLesson(formData);
            }

            this.hideLoading();

            // Mostrar mensaje de éxito
            const message = lessonId ? 'Lección actualizada correctamente' : 'Lección creada correctamente';
            if (typeof window.showToast === 'function') {
                window.showToast(message, 'success');
            }

            // Cerrar modal si existe
            const modal = document.querySelector('.modal.show');
            if (modal && typeof bootstrap !== 'undefined') {
                const bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) {
                    bsModal.hide();
                }
            }

            // Recargar tabla o lista si existe una función
            if (typeof window.reloadLessonsTable === 'function') {
                window.reloadLessonsTable();
            }

            return result;
        } catch (error) {
            this.hideLoading();
            
            // Mostrar errores de validación
            if (error.message.includes('validation') || error.message.includes('validación')) {
                this.showValidationErrors(error);
            } else {
                // Mostrar error general
                if (typeof window.showToast === 'function') {
                    window.showToast(`Error: ${error.message}`, 'error');
                } else {
                    alert(`Error: ${error.message}`);
                }
            }
        }
    }

    /**
     * Obtiene los datos del formulario manualmente
     * @returns {Object} - Datos del formulario
     */
    getFormDataManually() {
        return {
            course_id: document.getElementById('course_id')?.value,
            name: document.getElementById('name')?.value,
            level_number: document.getElementById('level_number')?.value,
            description: document.getElementById('description')?.value,
            difficulty: document.getElementById('difficulty')?.value,
            time_minutes: document.getElementById('time_minutes')?.value,
            content: document.getElementById('content')?.value
        };
    }

    /**
     * Valida los datos del formulario
     * @param {Object} formData - Datos a validar
     * @returns {boolean} - True si es válido
     */
    validateFormData(formData) {
        const errors = [];

        if (!formData.course_id) errors.push('Debe seleccionar un curso');
        if (!formData.name) errors.push('El nombre es requerido');
        if (!formData.level_number) errors.push('El nivel es requerido');
        if (!formData.description) errors.push('La descripción es requerida');
        if (!formData.difficulty) errors.push('La dificultad es requerida');
        if (!formData.time_minutes) errors.push('La duración es requerida');
        if (!formData.content) errors.push('El contenido es requerido');

        if (errors.length > 0) {
            const message = 'Errores de validación:\n' + errors.join('\n');
            if (typeof window.showToast === 'function') {
                window.showToast(message, 'error');
            } else {
                alert(message);
            }
            return false;
        }

        return true;
    }

    /**
     * Muestra errores de validación en el formulario
     * @param {Error} error - Error de validación
     */
    showValidationErrors(error) {
        // Limpiar errores previos
        document.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });

        // Mostrar mensaje general
        if (typeof window.showToast === 'function') {
            window.showToast(error.message, 'error');
        }
    }

    /**
     * Muestra un indicador de carga
     * @param {string} message - Mensaje a mostrar
     */
    showLoading(message = 'Cargando...') {
        // Buscar botón de envío y deshabilitarlo
        const submitBtn = document.querySelector('form button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                ${message}
            `;
        }
    }

    /**
     * Oculta el indicador de carga
     */
    hideLoading() {
        // Buscar botón de envío y habilitarlo
        const submitBtn = document.querySelector('form button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = submitBtn.dataset.originalText || 'Guardar lección';
        }
    }
}

// Instancia global del gestor
window.lessonManager = new LessonManager();

// Event listeners cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Guardar texto original del botón
    const submitBtn = document.querySelector('form button[type="submit"]');
    if (submitBtn && !submitBtn.dataset.originalText) {
        submitBtn.dataset.originalText = submitBtn.innerHTML;
    }

    // Manejar envío del formulario de lección
    const lessonForm = document.getElementById('lessonForm');
    if (lessonForm) {
        lessonForm.addEventListener('submit', function(event) {
            // Obtener ID de lección si está editando
            const lessonId = lessonForm.dataset.lessonId || null;
            window.lessonManager.handleFormSubmit(event, lessonId);
        });
    }
});