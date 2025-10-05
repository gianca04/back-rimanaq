@extends('layouts.app')

@section('title', 'Gestión de Cursos')

@section('content')
<div class="container-fluid px-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
            <p class="text-muted">Administra los cursos de lengua de señas</p>
        </div>
        <a href="{{ $createUrl }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Crear Curso
        </a>
    </div>

    <!-- Alert Container -->
    <div id="alert-container"></div>

    <!-- Loading Spinner -->
    <div id="loading-spinner" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
        <p class="mt-2 text-muted">Cargando cursos...</p>
    </div>

    <!-- Courses Grid -->
    <div id="courses-container" class="row g-4" style="display: none;">
        <!-- Los cursos se cargarán aquí dinámicamente -->
    </div>

    <!-- Empty State -->
    <div id="empty-state" class="text-center py-5" style="display: none;">
        <div class="mb-4">
            <i class="fas fa-graduation-cap fa-4x text-muted"></i>
        </div>
        <h4 class="text-muted">No hay cursos disponibles</h4>
        <p class="text-muted mb-4">Comienza creando tu primer curso de lengua de señas</p>
        <a href="{{ $createUrl }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Crear Primer Curso
        </a>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar el curso <strong id="course-name-delete"></strong>?</p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirm-delete">Eliminar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
/**
 * Controlador principal para la vista de índice de cursos
 * Maneja la carga, visualización y eliminación de cursos
 */
class CoursesIndexController {
    /**
     * Constructor - Inicializa la configuración y elementos del DOM
     */
    constructor() {
        this.config = {
            apiUrl: '{{ $apiUrl }}',
            token: this.getAuthToken(),
            csrfToken: '{{ csrf_token() }}',
            createUrl: '{{ $createUrl }}'
        };

        this.elements = {
            container: document.getElementById('courses-container'),
            loading: document.getElementById('loading-spinner'),
            emptyState: document.getElementById('empty-state'),
            alertContainer: document.getElementById('alert-container'),
            deleteModal: null,
            courseNameDelete: document.getElementById('course-name-delete'),
            confirmDelete: document.getElementById('confirm-delete')
        };

        this.state = {
            courses: [],
            currentCourseToDelete: null
        };

        this.init();
    }

    /**
     * Inicializa la aplicación
     */
    init() {
        this.initializeBootstrapComponents();
        this.bindEvents();
        this.loadCourses();
    }

    /**
     * Inicializa componentes de Bootstrap
     */
    initializeBootstrapComponents() {
        this.elements.deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    }

    /**
     * Vincula eventos del DOM
     */
    bindEvents() {
        this.elements.confirmDelete.addEventListener('click', () => {
            if (this.state.currentCourseToDelete) {
                this.deleteCourse(this.state.currentCourseToDelete);
            }
        });
    }

    /**
     * Obtiene el token de autenticación
     * @returns {string} Token de autenticación
     */
    getAuthToken() {
        // Buscar en localStorage, sessionStorage o meta tag
        return localStorage.getItem('auth_token') || 
               sessionStorage.getItem('auth_token') || 
               document.querySelector('meta[name="api-token"]')?.getAttribute('content') || 
               '';
    }

    /**
     * Carga los cursos desde la API
     */
    async loadCourses() {
        try {
            this.showLoading(true);

            const response = await fetch(this.config.apiUrl, {
                method: 'GET',
                headers: this.getHeaders()
            });

            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }

            const data = await response.json();
            this.state.courses = Array.isArray(data) ? data : (data.data || []);
            this.renderCourses();

        } catch (error) {
            console.error('Error al cargar cursos:', error);
            this.showAlert('Error al cargar los cursos. Por favor, verifica tu conexión e intenta nuevamente.', 'danger');
            this.showEmptyState();
        } finally {
            this.showLoading(false);
        }
    }

    /**
     * Renderiza la lista de cursos
     */
    renderCourses() {
        if (!this.state.courses || this.state.courses.length === 0) {
            this.showEmptyState();
            return;
        }

        this.elements.container.innerHTML = '';
        this.state.courses.forEach(course => {
            this.elements.container.appendChild(this.createCourseCard(course));
        });

        this.elements.container.style.display = 'block';
        this.elements.emptyState.style.display = 'none';
    }

    /**
     * Crea una tarjeta de curso
     * @param {Object} course - Datos del curso
     * @returns {HTMLElement} Elemento de la tarjeta
     */
    createCourseCard(course) {
        const col = document.createElement('div');
        col.className = 'col-md-6 col-lg-4';

        col.innerHTML = `
            <div class="card h-100 shadow-sm border-0 course-card">
                <div class="card-header border-0" style="background: linear-gradient(135deg, ${course.color || '#3498db'}, ${this.lightenColor(course.color || '#3498db', 20)});">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="text-white mb-0 fw-bold">
                            <i class="fas fa-graduation-cap me-2"></i>Curso
                        </h6>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li>
                                    <a class="dropdown-item" href="/dashboard/courses/${course.id}">
                                        <i class="fas fa-eye me-2 text-info"></i>Ver Detalles
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="/dashboard/courses/${course.id}/edit">
                                        <i class="fas fa-edit me-2 text-warning"></i>Editar
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <button class="dropdown-item text-danger" onclick="coursesController.confirmDelete(${course.id}, '${this.escapeHtml(course.name)}')">
                                        <i class="fas fa-trash me-2"></i>Eliminar
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title mb-3 fw-bold">${this.escapeHtml(course.name)}</h5>
                    <p class="card-text text-muted mb-3 flex-grow-1">${this.escapeHtml(course.description || 'Sin descripción disponible')}</p>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3 pt-2 border-top">
                        <small class="text-muted d-flex align-items-center">
                            <i class="fas fa-calendar-alt me-1"></i>
                            ${this.formatDate(course.created_at)}
                        </small>
                        <span class="badge bg-light text-dark border">ID: ${course.id}</span>
                    </div>
                </div>
                
                <div class="card-footer bg-transparent border-0 pt-0">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="/dashboard/lessons?course_id=${course.id}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-book me-1"></i>Lecciones
                        </a>
                        <a href="/dashboard/courses/${course.id}/edit" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit me-1"></i>Editar
                        </a>
                    </div>
                </div>
            </div>
        `;

        return col;
    }

    /**
     * Confirma la eliminación de un curso
     * @param {number} courseId - ID del curso
     * @param {string} courseName - Nombre del curso
     */
    confirmDelete(courseId, courseName) {
        this.state.currentCourseToDelete = courseId;
        this.elements.courseNameDelete.textContent = courseName;
        this.elements.deleteModal.show();
    }

    /**
     * Elimina un curso
     * @param {number} courseId - ID del curso a eliminar
     */
    async deleteCourse(courseId) {
        try {
            const response = await fetch(`${this.config.apiUrl}/${courseId}`, {
                method: 'DELETE',
                headers: this.getHeaders()
            });

            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }

            // Actualizar estado local
            this.state.courses = this.state.courses.filter(course => course.id !== courseId);
            
            // Re-renderizar vista
            this.renderCourses();
            
            // Cerrar modal y mostrar mensaje
            this.elements.deleteModal.hide();
            this.showAlert('Curso eliminado exitosamente', 'success');

        } catch (error) {
            console.error('Error al eliminar curso:', error);
            this.showAlert('Error al eliminar el curso. Por favor, intenta nuevamente.', 'danger');
        }
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
     * Muestra/oculta el indicador de carga
     * @param {boolean} show - Si mostrar o no el loading
     */
    showLoading(show) {
        this.elements.loading.style.display = show ? 'block' : 'none';
        if (!show) {
            this.elements.container.style.display = 'block';
        }
    }

    /**
     * Muestra el estado vacío
     */
    showEmptyState() {
        this.elements.container.style.display = 'none';
        this.elements.emptyState.style.display = 'block';
    }

    /**
     * Muestra una alerta
     * @param {string} message - Mensaje de la alerta
     * @param {string} type - Tipo de alerta (success, danger, warning, info)
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

        // Auto-remover después de 5 segundos
        setTimeout(() => {
            const alert = this.elements.alertContainer.querySelector('.alert');
            if (alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
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

    /**
     * Escapa caracteres HTML para prevenir XSS
     * @param {string} text - Texto a escapar
     * @returns {string} Texto escapado
     */
    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Formatea una fecha para mostrarla
     * @param {string} dateString - Fecha en formato ISO
     * @returns {string} Fecha formateada
     */
    formatDate(dateString) {
        if (!dateString) return 'No disponible';
        
        try {
            const date = new Date(dateString);
            return date.toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        } catch (error) {
            return 'Fecha inválida';
        }
    }

    /**
     * Aclara un color hexadecimal
     * @param {string} color - Color en formato hex
     * @param {number} percent - Porcentaje para aclarar
     * @returns {string} Color aclarado
     */
    lightenColor(color, percent) {
        // Convertir hex a RGB
        const hex = color.replace('#', '');
        const r = parseInt(hex.substr(0, 2), 16);
        const g = parseInt(hex.substr(2, 2), 16);
        const b = parseInt(hex.substr(4, 2), 16);

        // Aclarar
        const newR = Math.min(255, r + Math.round((255 - r) * percent / 100));
        const newG = Math.min(255, g + Math.round((255 - g) * percent / 100));
        const newB = Math.min(255, b + Math.round((255 - b) * percent / 100));

        // Convertir de vuelta a hex
        return `#${newR.toString(16).padStart(2, '0')}${newG.toString(16).padStart(2, '0')}${newB.toString(16).padStart(2, '0')}`;
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.coursesController = new CoursesIndexController();
});
</script>
@endsection

@section('styles')
<style>
    .course-card {
        transition: all 0.3s ease;
        border-radius: 0.75rem !important;
        overflow: hidden;
    }

    .course-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
    }

    .card-header {
        border: none !important;
        padding: 1.25rem;
    }

    .spinner-border {
        width: 3rem;
        height: 3rem;
    }

    .alert {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .btn {
        border-radius: 0.375rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .dropdown-menu {
        border: none;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        border-radius: 0.5rem;
        padding: 0.5rem;
    }

    .dropdown-item {
        border-radius: 0.375rem;
        padding: 0.5rem 1rem;
        transition: all 0.2s ease;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
        transform: translateX(4px);
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.4rem 0.6rem;
        border-radius: 0.375rem;
    }

    #empty-state i {
        opacity: 0.6;
    }

    .card-body {
        padding: 1.25rem;
    }

    .card-footer {
        padding: 0 1.25rem 1.25rem 1.25rem;
    }

    .text-gray-800 {
        color: #5a5c69 !important;
    }
</style>
@endsection