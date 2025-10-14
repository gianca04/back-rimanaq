@extends('layouts.app')

@section('title', 'Demo - CRUD de Contenido de Lecciones')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Demo - CRUD de Contenido</h2>
            <p class="text-muted mb-0">Prueba el sistema de gestión de contenido para lecciones</p>
        </div>
        <a href="{{ route('web.lessons.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver a lecciones
        </a>
    </div>

    <div class="row">
        {{-- Panel de Control --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-gear"></i> Panel de Control</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-success" onclick="addSampleContent()">
                            <i class="bi bi-plus-circle"></i> Agregar Contenido de Muestra
                        </button>
                        <button class="btn btn-primary" onclick="loadTestData()">
                            <i class="bi bi-file-code"></i> Cargar JSON de Prueba
                        </button>
                        <button class="btn btn-warning" onclick="showCurrentJson()">
                            <i class="bi bi-code-square"></i> Ver JSON Actual
                        </button>
                        <button class="btn btn-info" onclick="validateContent()">
                            <i class="bi bi-check-circle"></i> Validar Contenido
                        </button>
                        <button class="btn btn-danger" onclick="clearAllContent()">
                            <i class="bi bi-trash"></i> Limpiar Todo
                        </button>
                    </div>

                    <hr>

                    <h6>Estadísticas</h6>
                    <ul class="list-unstyled">
                        <li><strong>Total items:</strong> <span id="stats-total">0</span></li>
                        <li><strong>Con imagen:</strong> <span id="stats-images">0</span></li>
                        <li><strong>Con video:</strong> <span id="stats-videos">0</span></li>
                        <li><strong>Tamaño JSON:</strong> <span id="stats-size">0 KB</span></li>
                    </ul>
                </div>
            </div>

            {{-- JSON Viewer --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-file-earmark-code"></i> JSON Actual</h6>
                </div>
                <div class="card-body">
                    <pre id="json-display" class="bg-light p-2 rounded" style="font-size: 0.8em; max-height: 300px; overflow-y: auto;">[]</pre>
                </div>
            </div>
        </div>

        {{-- Formulario de Contenido --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-collection"></i> Gestión de Contenido</h5>
                </div>
                <div class="card-body">
                    {{-- Campo hidden para el contenido --}}
                    <input type="hidden" name="content" id="content" value='[]'>
                    
                    {{-- Incluir el formulario de contenido --}}
                    @include('lesson.content-form')
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal para JSON personalizado --}}
<div class="modal fade" id="jsonModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cargar JSON Personalizado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="custom-json" class="form-label">JSON del Contenido:</label>
                    <textarea id="custom-json" class="form-control" rows="10" placeholder='[{"index": 0, "titulo": "...", "descripcion": "...", "contenido": "...", "media": {"tipo": "image", "url": "..."}}]'></textarea>
                    <div class="form-text">Pega aquí un JSON válido con el formato de contenido de lección</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="loadCustomJson()">Cargar JSON</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎯 Demo de CRUD de contenido iniciado');
    
    // Actualizar estadísticas cada vez que cambie el contenido
    const observer = new MutationObserver(updateStats);
    const contentContainer = document.getElementById('content-items-container');
    if (contentContainer) {
        observer.observe(contentContainer, { childList: true, subtree: true });
    }
    
    // También observar cambios en el campo hidden
    const hiddenField = document.getElementById('content');
    if (hiddenField) {
        const inputObserver = new MutationObserver(updateStats);
        inputObserver.observe(hiddenField, { attributes: true, attributeFilter: ['value'] });
        
        // También escuchar evento input
        hiddenField.addEventListener('input', updateStats);
    }
    
    updateStats();
});

// Agregar contenido de muestra
function addSampleContent() {
    const sampleData = {
        index: Date.now(),
        titulo: "Contenido de Ejemplo " + (Math.floor(Math.random() * 100) + 1),
        descripcion: "Esta es una descripción de ejemplo para demostrar el funcionamiento del CRUD.",
        contenido: "Aquí va el contenido detallado de la lección. Puede incluir instrucciones paso a paso, explicaciones detalladas, ejercicios y cualquier información relevante para el aprendizaje.",
        media: {
            tipo: "image",
            url: "https://picsum.photos/400/300?random=" + Math.floor(Math.random() * 1000)
        }
    };
    
    // Usar la función global para agregar
    if (typeof window.jsonToContentArray === 'function') {
        const currentData = window.getContentData ? window.getContentData() : [];
        currentData.push(sampleData);
        window.jsonToContentArray(JSON.stringify(currentData));
        
        if (window.showToast) {
            window.showToast('Contenido de muestra agregado correctamente', 'success');
        }
    }
}

// Cargar datos de prueba completos
function loadTestData() {
    const testData = [
        {
            index: 0,
            titulo: "Introducción al Lenguaje de Señas",
            descripcion: "Aprende los conceptos básicos del lenguaje de señas peruano",
            contenido: "En esta primera lección aprenderás la importancia del lenguaje de señas como medio de comunicación para la comunidad sorda. Conocerás la historia, estructura básica y principios fundamentales que guían este hermoso lenguaje visual.",
            media: {
                tipo: "video",
                url: "https://www.youtube.com/watch?v=dQw4w9WgXcQ"
            }
        },
        {
            index: 1,
            titulo: "Alfabeto Manual",
            descripcion: "Domina las 26 letras del alfabeto en lenguaje de señas",
            contenido: "El alfabeto manual es la base fundamental para deletrear palabras. Cada letra tiene su posición específica de la mano. Practica cada letra lentamente y asegúrate de formar bien cada seña antes de pasar a la siguiente.",
            media: {
                tipo: "image",
                url: "https://i.pinimg.com/474x/59/5e/ef/595eef2e2109829e64648b4438802849.jpg"
            }
        },
        {
            index: 2,
            titulo: "Números del 1 al 10",
            descripcion: "Aprende a representar los números básicos",
            contenido: "Los números son esenciales para la comunicación diaria. En esta sección aprenderás las señas para los números del 1 al 10, que forman la base para todos los demás números. Presta atención a la orientación de la palma y la posición de los dedos.",
            media: {
                tipo: "video",
                url: "https://www.youtube.com/shorts/72kThwEHfqY"
            }
        }
    ];
    
    if (typeof window.jsonToContentArray === 'function') {
        window.jsonToContentArray(JSON.stringify(testData));
        
        if (window.showToast) {
            window.showToast('Datos de prueba cargados correctamente', 'success');
        }
    }
}

// Mostrar JSON actual
function showCurrentJson() {
    if (typeof window.updateHiddenJson === 'function') {
        window.updateHiddenJson();
    }
    
    const hiddenField = document.getElementById('content');
    const jsonDisplay = document.getElementById('json-display');
    
    if (hiddenField && jsonDisplay) {
        try {
            const jsonData = JSON.parse(hiddenField.value || '[]');
            jsonDisplay.textContent = JSON.stringify(jsonData, null, 2);
        } catch (error) {
            jsonDisplay.textContent = 'Error: JSON no válido';
        }
    }
    
    updateStats();
}

// Validar contenido
function validateContent() {
    if (typeof window.validateContentForm === 'function') {
        const validation = window.validateContentForm();
        
        if (validation.isValid) {
            if (window.showToast) {
                window.showToast('✅ Contenido válido - Sin errores encontrados', 'success');
            } else {
                alert('✅ Contenido válido - Sin errores encontrados');
            }
        } else {
            const errorMsg = '❌ Errores encontrados:\n\n' + validation.errors.join('\n');
            if (window.showToast) {
                window.showToast(errorMsg, 'error');
            } else {
                alert(errorMsg);
            }
        }
    }
}

// Limpiar todo el contenido
function clearAllContent() {
    if (confirm('¿Estás seguro de eliminar todo el contenido?')) {
        if (typeof window.clearContentForm === 'function') {
            window.clearContentForm();
            
            if (window.showToast) {
                window.showToast('Todo el contenido ha sido eliminado', 'info');
            }
        }
        updateStats();
    }
}

// Cargar JSON personalizado
function loadCustomJson() {
    const customJsonText = document.getElementById('custom-json').value.trim();
    
    if (!customJsonText) {
        alert('Por favor, ingresa un JSON válido');
        return;
    }
    
    try {
        // Validar JSON
        const parsedJson = JSON.parse(customJsonText);
        
        if (!Array.isArray(parsedJson)) {
            throw new Error('El JSON debe ser un array');
        }
        
        // Cargar el JSON
        if (typeof window.jsonToContentArray === 'function') {
            window.jsonToContentArray(customJsonText);
            
            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('jsonModal'));
            if (modal) {
                modal.hide();
            }
            
            if (window.showToast) {
                window.showToast('JSON personalizado cargado correctamente', 'success');
            }
        }
    } catch (error) {
        alert('Error en el JSON: ' + error.message);
    }
}

// Actualizar estadísticas
function updateStats() {
    const hiddenField = document.getElementById('content');
    
    if (!hiddenField) return;
    
    try {
        const data = JSON.parse(hiddenField.value || '[]');
        
        // Contar estadísticas
        const total = data.length;
        const images = data.filter(item => item.media && item.media.tipo === 'image').length;
        const videos = data.filter(item => item.media && item.media.tipo === 'video').length;
        const jsonSize = (new Blob([hiddenField.value]).size / 1024).toFixed(2);
        
        // Actualizar display
        document.getElementById('stats-total').textContent = total;
        document.getElementById('stats-images').textContent = images;
        document.getElementById('stats-videos').textContent = videos;
        document.getElementById('stats-size').textContent = jsonSize + ' KB';
        
        // Actualizar JSON display
        const jsonDisplay = document.getElementById('json-display');
        if (jsonDisplay) {
            jsonDisplay.textContent = JSON.stringify(data, null, 2);
        }
        
    } catch (error) {
        console.error('Error actualizando estadísticas:', error);
    }
}

// Mostrar modal de JSON personalizado
function showJsonModal() {
    const modal = new bootstrap.Modal(document.getElementById('jsonModal'));
    modal.show();
}

// Exportar datos actuales
function exportCurrentData() {
    if (typeof window.updateHiddenJson === 'function') {
        window.updateHiddenJson();
    }
    
    const hiddenField = document.getElementById('content');
    if (hiddenField) {
        const dataStr = hiddenField.value;
        const dataBlob = new Blob([dataStr], {type: 'application/json'});
        const url = URL.createObjectURL(dataBlob);
        
        const link = document.createElement('a');
        link.href = url;
        link.download = 'lesson-content-' + new Date().toISOString().slice(0,10) + '.json';
        link.click();
        
        URL.revokeObjectURL(url);
        
        if (window.showToast) {
            window.showToast('Datos exportados correctamente', 'success');
        }
    }
}
</script>

<style>
.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    transition: box-shadow 0.3s ease;
}

#json-display {
    font-family: 'Courier New', monospace;
    font-size: 0.75em;
    line-height: 1.2;
    white-space: pre-wrap;
    word-break: break-all;
}

.stats-badge {
    font-size: 0.9em;
    padding: 0.25rem 0.5rem;
}
</style>
@endsection