@extends('layouts.app')

@section('title', 'Gestos')

@section('content')
<div class="container-fluid px-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Gestión de Gestos</h1>
            <p class="text-muted">Administra los gestos de lengua de señas</p>
        </div>
        <a href="{{ route('web.gestures.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Crear Gesto
        </a>
    </div>

    <!-- Alerts Section -->
    <div id="alert-container"></div>

    <!-- Loading Spinner -->
    <div id="loading-spinner" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
        <p class="mt-2 text-muted">Cargando gestos...</p>
    </div>

    <!-- Gestures Table -->
    <div id="gestures-container" style="display: none;">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nombre del Gesto</th>
                                <th>Lección</th>
                                <th>Frames</th>
                                <th>Fecha de Creación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="gestures-tbody">
                            <!-- Los gestos se cargarán aquí dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Empty State -->
    <div id="empty-state" class="text-center py-5" style="display: none;">
        <div class="mb-4">
            <i class="fas fa-hand-paper fa-4x text-muted"></i>
        </div>
        <h4 class="text-muted">No hay gestos disponibles</h4>
        <p class="text-muted mb-4">Comienza creando tu primer gesto de lengua de señas</p>
        <a href="{{ route('web.gestures.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Crear Primer Gesto
        </a>
    </div>
</div>
@endsection

@section('scripts')
<script type="module">
    // Rutas API
    const apiBase = '/api/gestures';
    const token = localStorage.getItem('auth_token');

    // Elementos
    const tableBody = document.getElementById('gestures-tbody');
    const loadingSpinner = document.getElementById('loading-spinner');
    const gesturesContainer = document.getElementById('gestures-container');
    const emptyState = document.getElementById('empty-state');
    const alertContainer = document.getElementById('alert-container');

    // Renderizar gestos
    async function fetchGestures() {
        showLoading(true);
        
        try {
            const res = await fetch(apiBase, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            
            const data = await res.json();
            
            if (!res.ok) {
                throw new Error(data.message || 'Error al cargar gestos');
            }
            
            renderGestures(data.data || []);
            
        } catch (error) {
            console.error('Error:', error);
            showAlert('Error al cargar los gestos: ' + error.message, 'danger');
            showEmptyState();
        } finally {
            showLoading(false);
        }
    }

    function renderGestures(gestures) {
        if (!gestures || gestures.length === 0) {
            showEmptyState();
            return;
        }

        tableBody.innerHTML = '';
        gestures.forEach(gesture => {
            const gestureName = gesture.gesture_data?.name || 'Sin nombre';
            const frameCount = gesture.gesture_data?.frameCount || 0;
            const lessonName = gesture.lesson?.name || 'Sin lección';
            
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <span class="badge bg-primary">${gesture.id}</span>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-hand-paper text-primary me-2"></i>
                        <strong>${escapeHtml(gestureName)}</strong>
                    </div>
                </td>
                <td>
                    <span class="text-muted">${escapeHtml(lessonName)}</span>
                </td>
                <td>
                    <span class="badge bg-info">${frameCount} frames</span>
                </td>
                <td>
                    <small class="text-muted">
                        <i class="fas fa-calendar me-1"></i>
                        ${formatDate(gesture.created_at)}
                    </small>
                </td>
                <td>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewGesture(${gesture.id})" title="Ver">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="editGesture(${gesture.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteGesture(${gesture.id})" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            `;
            tableBody.appendChild(tr);
        });

        gesturesContainer.style.display = 'block';
        emptyState.style.display = 'none';
    }

    window.viewGesture = function(id) {
        window.location.href = `/gestures/${id}`;
    }

    window.editGesture = function(id) {
        window.location.href = `/gestures/${id}/edit`;
    }

    window.deleteGesture = async function(id) {
        if (!confirm('¿Eliminar este gesto?')) return;

        try {
            const res = await fetch(`${apiBase}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await res.json();

            if (!res.ok) {
                throw new Error(data.message || 'Error al eliminar gesto');
            }

            showAlert('Gesto eliminado exitosamente', 'success');
            fetchGestures();

        } catch (error) {
            console.error('Error:', error);
            showAlert('Error al eliminar el gesto: ' + error.message, 'danger');
        }
    }

    function showLoading(show) {
        loadingSpinner.style.display = show ? 'block' : 'none';
        gesturesContainer.style.display = show ? 'none' : 'block';
        emptyState.style.display = 'none';
    }

    function showEmptyState() {
        gesturesContainer.style.display = 'none';
        emptyState.style.display = 'block';
        loadingSpinner.style.display = 'none';
    }

    function showAlert(message, type = 'info') {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="fas fa-${getAlertIcon(type)} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        alertContainer.innerHTML = alertHtml;

        setTimeout(() => {
            const alert = alertContainer.querySelector('.alert');
            if (alert) alert.remove();
        }, 5000);
    }

    function getAlertIcon(type) {
        const icons = {
            'success': 'check-circle',
            'danger': 'exclamation-triangle',
            'warning': 'exclamation-circle',
            'info': 'info-circle'
        };
        return icons[type] || 'info-circle';
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    // Inicializar
    fetchGestures();
</script>
@endsection

@section('styles')
<style>
    .table-hover tbody tr:hover {
        background-color: rgba(52, 152, 219, 0.1);
    }

    .spinner-border {
        width: 3rem;
        height: 3rem;
    }

    .alert {
        border: none;
        border-radius: 0.5rem;
    }

    .btn {
        border-radius: 0.375rem;
    }

    .btn-group .btn {
        border-radius: 0.25rem;
    }

    .card {
        border: none;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .badge {
        font-size: 0.75em;
    }

    .table th {
        border-top: none;
        font-weight: 600;
        color: #495057;
        background-color: #f8f9fa;
    }

    .table td {
        vertical-align: middle;
        border-color: #e9ecef;
    }
</style>
@endsection