@extends('layouts.app')

@section('title', 'Crear Curso')

@section('content')
<div class="container-fluid px-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
            <p class="text-muted">Crea un nuevo curso de lengua de señas</p>
        </div>
        <a href="{{ $indexUrl }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver a la lista
        </a>
    </div>

    <!-- Alert Container -->
    <div id="alert-container"></div>

    <!-- Course Form -->
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-graduation-cap me-2"></i>
                        Información del Curso
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form id="courseForm" novalidate>
                        <!-- Nombre del Curso -->
                        <div class="mb-4">
                            <label for="courseName" class="form-label fw-bold">
                                <i class="fas fa-tag me-1 text-primary"></i>
                                Nombre del Curso *
                            </label>
                            <input 
                                type="text" 
                                class="form-control form-control-lg" 
                                id="courseName" 
                                name="name"
                                placeholder="Ej. Curso Básico de Lengua de Señas Peruana"
                                required
                                maxlength="255"
                            >
                            <div class="invalid-feedback"></div>
                            <div class="form-text">Ingresa un nombre descriptivo para el curso</div>
                        </div>

                        <!-- Descripción -->
                        <div class="mb-4">
                            <label for="courseDescription" class="form-label fw-bold">
                                <i class="fas fa-align-left me-1 text-primary"></i>
                                Descripción
                            </label>
                            <textarea 
                                class="form-control" 
                                id="courseDescription" 
                                name="description"
                                rows="4"
                                placeholder="Describe brevemente el contenido y objetivos del curso..."
                                maxlength="1000"
                            ></textarea>
                            <div class="invalid-feedback"></div>
                            <div class="form-text">
                                <span id="description-counter">0</span>/1000 caracteres
                            </div>
                        </div>

                        <!-- Color del Curso -->
                        <div class="mb-4">
                            <label for="courseColor" class="form-label fw-bold">
                                <i class="fas fa-palette me-1 text-primary"></i>
                                Color del Curso
                            </label>
                            <div class="d-flex align-items-center">
                                <input 
                                    type="color" 
                                    class="form-control form-control-color" 
                                    id="courseColor" 
                                    name="color"
                                    value="#3498db"
                                    title="Selecciona un color para el curso"
                                >
                                <div class="ms-3">
                                    <div class="color-preview rounded-circle d-inline-block" 
                                         id="colorPreview" 
                                         style="width: 40px; height: 40px; background-color: #3498db; border: 2px solid #dee2e6;">
                                    </div>
                                    <div class="form-text mt-1">
                                        Código: <span id="colorCode">#3498db</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-text">Este color se usará para identificar visualmente el curso</div>
                        </div>

                        <!-- Imagen del Curso (Opcional) -->
                        <div class="mb-4">
                            <label for="courseImage" class="form-label fw-bold">
                                <i class="fas fa-image me-1 text-primary"></i>
                                Ruta de Imagen (Opcional)
                            </label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="courseImage" 
                                name="image_path"
                                placeholder="Ej. /images/curso-basico.jpg"
                                maxlength="500"
                            >
                            <div class="invalid-feedback"></div>
                            <div class="form-text">Ruta relativa a la imagen representativa del curso</div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="d-flex gap-3 justify-content-end pt-3 border-top">
                            <button type="button" class="btn btn-outline-secondary" onclick="window.location.href='{{ $indexUrl }}'">
                                <i class="fas fa-times me-2"></i>
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <span id="submitText">
                                    <i class="fas fa-save me-2"></i>
                                    Crear Curso
                                </span>
                                <span id="submitLoading" class="d-none">
                                    <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                    Creando...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
/**
 * Controlador para la vista de creación de cursos
 * Maneja la validación del formulario y el envío de datos a la API
 */
class CourseCreateController {
    /**
     * Constructor - Inicializa la configuración y elementos del DOM
     */
    constructor() {
        this.config = {
            apiUrl: '{{ $apiUrl }}',
            indexUrl: '{{ $indexUrl }}',
            token: this.getAuthToken(),
            csrfToken: '{{ csrf_token() }}'
        };

        this.elements = {
            form: document.getElementById('courseForm'),
            submitBtn: document.getElementById('submitBtn'),
            submitText: document.getElementById('submitText'),
            submitLoading: document.getElementById('submitLoading'),
            alertContainer: document.getElementById('alert-container'),
            colorInput: document.getElementById('courseColor'),
            colorPreview: document.getElementById('colorPreview'),
            colorCode: document.getElementById('colorCode'),
            descriptionTextarea: document.getElementById('courseDescription'),
            descriptionCounter: document.getElementById('description-counter')
        };

        this.validation = {
            rules: {
                name: {
                    required: true,
                    minLength: 3,
                    maxLength: 255
                },
                description: {
                    maxLength: 1000
                },
                color: {
                    pattern: /^#[0-9A-Fa-f]{6}$/
                },
                image_path: {
                    maxLength: 500,
                    pattern: /^\/.*\.(jpg|jpeg|png|gif|webp)$/i
                }
            }
        };

        this.init();
    }

    /**
     * Inicializa la aplicación
     */
    init() {
        this.bindEvents();
        this.initializeForm();
    }

    /**
     * Vincula eventos del DOM
     */
    bindEvents() {
        // Evento de envío del formulario
        this.elements.form.addEventListener('submit', (e) => this.handleSubmit(e));

        // Evento de cambio de color
        this.elements.colorInput.addEventListener('input', (e) => this.updateColorPreview(e.target.value));

        // Contador de caracteres para descripción
        this.elements.descriptionTextarea.addEventListener('input', (e) => this.updateDescriptionCounter(e.target.value));

        // Validación en tiempo real
        this.elements.form.querySelectorAll('input, textarea').forEach(field => {
            field.addEventListener('blur', () => this.validateField(field));
            field.addEventListener('input', () => this.clearFieldError(field));
        });
    }

    /**
     * Inicializa el formulario
     */
    initializeForm() {
        // Actualizar contador inicial
        this.updateDescriptionCounter(this.elements.descriptionTextarea.value);
        
        // Inicializar preview de color
        this.updateColorPreview(this.elements.colorInput.value);
    }

    /**
     * Maneja el envío del formulario
     * @param {Event} e - Evento de envío
     */
    async handleSubmit(e) {
        e.preventDefault();

        if (!this.validateForm()) {
            this.showAlert('Por favor, corrige los errores en el formulario', 'warning');
            return;
        }

        this.setLoading(true);

        try {
            const formData = new FormData(this.elements.form);
            const courseData = Object.fromEntries(formData);

            // Limpiar campos vacíos opcionales
            Object.keys(courseData).forEach(key => {
                if (!courseData[key] && key !== 'name') {
                    delete courseData[key];
                }
            });

            const response = await fetch(this.config.apiUrl, {
                method: 'POST',
                headers: this.getHeaders(),
                body: JSON.stringify(courseData)
            });

            const result = await response.json();

            if (!response.ok) {
                if (response.status === 422 && result.errors) {
                    this.showValidationErrors(result.errors);
                } else {
                    throw new Error(result.message || `Error HTTP: ${response.status}`);
                }
                return;
            }

            this.showAlert('Curso creado exitosamente', 'success');
            
            // Redirigir después de 1.5 segundos
            setTimeout(() => {
                window.location.href = this.config.indexUrl;
            }, 1500);

        } catch (error) {
            console.error('Error al crear curso:', error);
            this.showAlert('Error al crear el curso. Por favor, intenta nuevamente.', 'danger');
        } finally {
            this.setLoading(false);
        }
    }

    /**
     * Valida todo el formulario
     * @returns {boolean} True si el formulario es válido
     */
    validateForm() {
        let isValid = true;
        const fields = this.elements.form.querySelectorAll('input, textarea');

        fields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });

        return isValid;
    }

    /**
     * Valida un campo individual
     * @param {HTMLElement} field - Campo a validar
     * @returns {boolean} True si el campo es válido
     */
    validateField(field) {
        const value = field.value.trim();
        const name = field.name;
        const rules = this.validation.rules[name];

        if (!rules) return true;

        // Validar campo requerido
        if (rules.required && !value) {
            this.setFieldError(field, 'Este campo es requerido');
            return false;
        }

        // Validar longitud mínima
        if (rules.minLength && value.length < rules.minLength) {
            this.setFieldError(field, `Mínimo ${rules.minLength} caracteres`);
            return false;
        }

        // Validar longitud máxima
        if (rules.maxLength && value.length > rules.maxLength) {
            this.setFieldError(field, `Máximo ${rules.maxLength} caracteres`);
            return false;
        }

        // Validar patrón
        if (rules.pattern && value && !rules.pattern.test(value)) {
            const errorMessages = {
                color: 'Formato de color inválido',
                image_path: 'La ruta debe ser válida y terminar en .jpg, .jpeg, .png, .gif o .webp'
            };
            this.setFieldError(field, errorMessages[name] || 'Formato inválido');
            return false;
        }

        this.clearFieldError(field);
        return true;
    }

    /**
     * Establece un error en un campo
     * @param {HTMLElement} field - Campo con error
     * @param {string} message - Mensaje de error
     */
    setFieldError(field, message) {
        field.classList.add('is-invalid');
        field.classList.remove('is-valid');
        
        const feedback = field.parentNode.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.textContent = message;
        }
    }

    /**
     * Limpia el error de un campo
     * @param {HTMLElement} field - Campo a limpiar
     */
    clearFieldError(field) {
        field.classList.remove('is-invalid');
        if (field.value.trim()) {
            field.classList.add('is-valid');
        } else {
            field.classList.remove('is-valid');
        }
        
        const feedback = field.parentNode.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.textContent = '';
        }
    }

    /**
     * Muestra errores de validación del servidor
     * @param {Object} errors - Errores de validación
     */
    showValidationErrors(errors) {
        Object.keys(errors).forEach(fieldName => {
            const field = this.elements.form.querySelector(`[name="${fieldName}"]`);
            if (field) {
                this.setFieldError(field, errors[fieldName][0]);
            }
        });
    }

    /**
     * Actualiza la vista previa del color
     * @param {string} color - Color seleccionado
     */
    updateColorPreview(color) {
        this.elements.colorPreview.style.backgroundColor = color;
        this.elements.colorCode.textContent = color;
    }

    /**
     * Actualiza el contador de caracteres de la descripción
     * @param {string} text - Texto actual
     */
    updateDescriptionCounter(text) {
        const count = text.length;
        this.elements.descriptionCounter.textContent = count;
        
        // Cambiar color según proximidad al límite
        if (count > 900) {
            this.elements.descriptionCounter.className = 'text-danger fw-bold';
        } else if (count > 750) {
            this.elements.descriptionCounter.className = 'text-warning';
        } else {
            this.elements.descriptionCounter.className = '';
        }
    }

    /**
     * Establece el estado de carga del formulario
     * @param {boolean} loading - Si está cargando
     */
    setLoading(loading) {
        this.elements.submitBtn.disabled = loading;
        this.elements.submitText.classList.toggle('d-none', loading);
        this.elements.submitLoading.classList.toggle('d-none', !loading);

        // Deshabilitar todos los campos durante la carga
        const fields = this.elements.form.querySelectorAll('input, textarea, button');
        fields.forEach(field => {
            if (field !== this.elements.submitBtn) {
                field.disabled = loading;
            }
        });
    }

    /**
     * Obtiene el token de autenticación
     * @returns {string} Token de autenticación
     */
    getAuthToken() {
        return localStorage.getItem('auth_token') || 
               sessionStorage.getItem('auth_token') || 
               document.querySelector('meta[name="api-token"]')?.getAttribute('content') || 
               '';
    }

    /**
     * Obtiene los headers para las peticiones HTTP
     * @returns {Object} Headers de la petición
     */
    getHeaders() {
        const headers = {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        };

        if (this.config.token) {
            headers['Authorization'] = `Bearer ${this.config.token}`;
        }

        if (this.config.csrfToken) {
            headers['X-CSRF-TOKEN'] = this.config.csrfToken;
        }

        return headers;
    }

    /**
     * Muestra una alerta
     * @param {string} message - Mensaje de la alerta
     * @param {string} type - Tipo de alerta
     */
    showAlert(message, type = 'info') {
        const alertClass = `alert-${type}`;
        const alertIcon = this.getAlertIcon(type);

        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="fas fa-${alertIcon} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        this.elements.alertContainer.innerHTML = alertHtml;

        // Auto-remover después de 5 segundos para alertas de éxito
        if (type === 'success') {
            setTimeout(() => {
                const alert = this.elements.alertContainer.querySelector('.alert');
                if (alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 5000);
        }

        // Scroll hacia la alerta
        this.elements.alertContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    /**
     * Obtiene el ícono apropiado para el tipo de alerta
     * @param {string} type - Tipo de alerta
     * @returns {string} Nombre del ícono
     */
    getAlertIcon(type) {
        const icons = {
            'success': 'check-circle',
            'danger': 'exclamation-triangle',
            'warning': 'exclamation-circle',
            'info': 'info-circle'
        };
        return icons[type] || 'info-circle';
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.courseCreateController = new CourseCreateController();
});
</script>
@endsection

@section('styles')
<style>
    .form-control:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }

    .form-control.is-valid {
        border-color: #28a745;
    }

    .form-control.is-invalid {
        border-color: #dc3545;
    }

    .form-control-color {
        width: 60px;
        height: 40px;
        border-radius: 0.375rem;
        border: 2px solid #dee2e6;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .form-control-color:hover {
        border-color: #3498db;
        transform: scale(1.05);
    }

    .color-preview {
        transition: all 0.3s ease;
    }

    .card {
        border-radius: 0.75rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        border-radius: 0.75rem 0.75rem 0 0 !important;
        border: none;
    }

    .btn {
        border-radius: 0.375rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
    }

    .form-label {
        color: #495057;
        margin-bottom: 0.75rem;
    }

    .form-text {
        font-size: 0.875rem;
        color: #6c757d;
    }

    .alert {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
    }

    .text-gray-800 {
        color: #5a5c69 !important;
    }

    .border-top {
        border-color: #e3e6f0 !important;
    }
</style>
@endsection