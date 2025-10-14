{{-- Formulario CRUD para el contenido del array content --}}
<div id="content-form-container" class="border p-3 rounded bg-light">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="mb-0 text-primary">
            <i class="bi bi-collection"></i> Gestión de Contenido de la Lección
        </h6>
        <button type="button" id="add-content-item" class="btn btn-success btn-sm">
            <i class="bi bi-plus-circle"></i> Agregar Contenido
        </button>
    </div>

    {{-- Contenedor donde se renderizarán los items del content --}}
    <div id="content-items-container">
        {{-- Los items se cargan dinámicamente aquí --}}
    </div>

    {{-- Template para los items de contenido --}}
    <template id="content-item-template">
        <div class="content-item card mb-3" data-index="">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <span class="badge bg-primary me-2 content-index-badge">Item #</span>
                    <strong class="content-title-display text-truncate" style="max-width: 200px;">Sin título</strong>
                </div>
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-secondary toggle-content-item" title="Expandir/Contraer">
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <button type="button" class="btn btn-outline-primary move-up-content" title="Subir">
                        <i class="bi bi-arrow-up"></i>
                    </button>
                    <button type="button" class="btn btn-outline-primary move-down-content" title="Bajar">
                        <i class="bi bi-arrow-down"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger delete-content-item" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
            <div class="card-body content-item-body">
                <div class="row">
                    {{-- Campo Título --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Título *</label>
                        <input type="text" class="form-control content-titulo" placeholder="Ej: Introducción al alfabeto" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    {{-- Campo Index (oculto pero editable para debugging) --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Índice</label>
                        <input type="number" class="form-control content-index" min="0" readonly style="background-color: #f8f9fa;">
                        <div class="form-text">Se actualiza automáticamente</div>
                    </div>
                </div>

                {{-- Campo Descripción --}}
                <div class="mb-3">
                    <label class="form-label">Descripción *</label>
                    <textarea class="form-control content-descripcion" rows="2" placeholder="Breve descripción del contenido..." required></textarea>
                    <div class="invalid-feedback"></div>
                </div>

                {{-- Campo Contenido --}}
                <div class="mb-3">
                    <label class="form-label">Contenido detallado *</label>
                    <textarea class="form-control content-contenido" rows="4" placeholder="Describe en detalle el contenido, instrucciones, pasos, etc..." required></textarea>
                    <div class="invalid-feedback"></div>
                </div>

                {{-- Sección Media --}}
                <div class="border p-3 rounded bg-white">
                    <h6 class="text-secondary mb-3">
                        <i class="bi bi-camera-video"></i> Multimedia
                    </h6>
                    
                    <div class="row">
                        {{-- Tipo de Media --}}
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tipo de Media *</label>
                            <select class="form-select content-media-tipo" required>
                                <option value="">Seleccionar tipo...</option>
                                <option value="image">Imagen</option>
                                <option value="video">Video</option>
                                <option value="audio">Audio</option>
                                <option value="document">Documento</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        {{-- URL de Media --}}
                        <div class="col-md-8 mb-3">
                            <label class="form-label">URL del Media *</label>
                            <div class="input-group">
                                <input type="url" class="form-control content-media-url" placeholder="https://ejemplo.com/imagen.jpg o https://youtube.com/watch?v=..." required>
                                <button type="button" class="btn btn-outline-secondary preview-media" title="Vista previa">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback"></div>
                            <div class="form-text">
                                Soporta URLs de YouTube, imágenes, documentos, etc.
                            </div>
                        </div>
                    </div>

                    {{-- Preview del Media --}}
                    <div class="media-preview mt-3" style="display: none;">
                        <div class="border p-2 rounded bg-light">
                            <div class="media-preview-content">
                                {{-- El contenido de preview se carga aquí --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- Información de ayuda --}}
    <div class="mt-3">
        <small class="text-muted">
            <i class="bi bi-info-circle"></i> 
            Los elementos se guardan automáticamente. Usa las flechas para reordenar el contenido.
        </small>
    </div>
</div>

<style>
.content-item {
    transition: all 0.3s ease;
}

.content-item.collapsed .content-item-body {
    display: none;
}

.content-item.collapsed .toggle-content-item i {
    transform: rotate(-90deg);
}

.toggle-content-item i {
    transition: transform 0.3s ease;
}

.content-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.media-preview img {
    max-width: 100%;
    max-height: 200px;
    object-fit: contain;
}

.media-preview video {
    max-width: 100%;
    max-height: 200px;
}

.content-title-display {
    font-weight: 500;
}

.content-index-badge {
    font-size: 0.75em;
}

.btn-group-sm .btn {
    --bs-btn-padding-y: 0.25rem;
    --bs-btn-padding-x: 0.5rem;
    --bs-btn-font-size: 0.75rem;
}
</style>

{{-- Incluir el manager de CRUD --}}
<script src="{{ asset('js/content-crud-manager.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎯 Inicializando formulario de contenido con manager...');
    
    // El ContentCRUDManager se inicializa automáticamente
    // Las funciones globales se configuran automáticamente para compatibilidad
    
    // Escuchar eventos personalizados del manager
    document.addEventListener('contentUpdated', function(event) {
        console.log('� Contenido actualizado:', event.detail.data.length, 'items');
    });
    
    document.addEventListener('contentCleared', function(event) {
        console.log('🧹 Contenido limpiado');
    });
    
    console.log('✅ Formulario de contenido CRUD inicializado con manager');
});
</script>