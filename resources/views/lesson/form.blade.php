<form id="lessonForm">
    <div class="mb-3">
        <label for="course_id" class="form-label">Curso *</label>
        <select name="course_id" id="course_id" class="form-select" required>
            <option value="">Seleccionar curso...</option>
            <!-- Los cursos se cargan dinámicamente -->
        </select>
        <div class="invalid-feedback"></div>
    </div>

    <div class="row">
        <div class="col-md-8 mb-3">
            <label for="name" class="form-label">Nombre de la lección *</label>
            <input type="text" name="name" id="name" class="form-control" required placeholder="Ej: Alfabeto básico">
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-4 mb-3">
            <label for="level_number" class="form-label">Nivel *</label>
            <input type="number" name="level_number" id="level_number" class="form-control" min="1" max="100" required placeholder="1">
            <div class="invalid-feedback"></div>
        </div>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Descripción *</label>
        <textarea name="description" id="description" class="form-control" rows="3" required placeholder="Describe el contenido y objetivos de la lección..."></textarea>
        <div class="form-text">Mínimo 10 caracteres, máximo 2000</div>
        <div class="invalid-feedback"></div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="difficulty" class="form-label">Dificultad *</label>
            <select name="difficulty" id="difficulty" class="form-select" required>
                <option value="">Seleccionar dificultad...</option>
                <option value="fácil">Fácil</option>
                <option value="intermedio">Intermedio</option>
                <option value="difícil">Difícil</option>
            </select>
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-6 mb-3">
            <label for="time_minutes" class="form-label">Duración (minutos) *</label>
            <input type="number" name="time_minutes" id="time_minutes" class="form-control" min="5" max="600" required placeholder="30">
            <div class="form-text">Entre 5 y 600 minutos</div>
            <div class="invalid-feedback"></div>
        </div>
    </div>

    <!-- Content -->
    <div class="mb-3">
        <label class="form-label">Contenido de la lección *</label>
        <input type="hidden" name="content" id="content" value='[]'>
        @include('lesson.content-form')
        <div class="invalid-feedback"></div>
    </div>



    <div class="d-flex justify-content-between align-items-center">
        <div class="btn-group">
            <button type="button" class="btn btn-outline-info btn-sm" onclick="debugContentForm()" title="Debug del contenido">
                <i class="bi bi-bug"></i> Debug
            </button>
            <button type="button" class="btn btn-outline-warning btn-sm" onclick="loadTestContentData()" title="Cargar datos de prueba">
                <i class="bi bi-file-code"></i> Test Data
            </button>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar lección</button>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Función para cargar datos de la lección al editar
        window.loadLessonData = function(lessonData) {
            console.log('📥 Cargando datos de lección:', lessonData);
            
            if (lessonData) {
                // PASO 1: Reinicializar completamente el formulario
                console.log('🔄 PASO 1: Reinicializando formulario...');
                window.reinitializeLessonForm();
                
                // PASO 2: Esperar y cargar los datos básicos
                setTimeout(() => {
                    console.log('📝 PASO 2: Cargando datos básicos...');
                    
                    document.getElementById('course_id').value = lessonData.course_id || '';
                    document.getElementById('name').value = lessonData.name || '';
                    document.getElementById('level_number').value = lessonData.level_number || '';
                    document.getElementById('description').value = lessonData.description || '';
                    document.getElementById('difficulty').value = lessonData.difficulty || '';
                    document.getElementById('time_minutes').value = lessonData.time_minutes || '';
                    
                    // PASO 3: Cargar contenido específico
                    console.log('🎯 PASO 3: Cargando contenido...');
                    
                    const contentField = document.getElementById('content');
                    
                    // Manejar el contenido que viene del API
                    let contentValue = '[]'; // Valor por defecto
                    
                    if (lessonData.content !== null && lessonData.content !== undefined) {
                        if (typeof lessonData.content === 'string') {
                            // Si es string, usarlo directamente
                            contentValue = lessonData.content;
                        } else if (Array.isArray(lessonData.content)) {
                            // Si es array, convertir a JSON string
                            contentValue = JSON.stringify(lessonData.content);
                        } else if (typeof lessonData.content === 'object') {
                            // Si es objeto, convertir a JSON string
                            contentValue = JSON.stringify(lessonData.content);
                        }
                    }
                    
                    console.log('📝 Contenido del API:', lessonData.content);
                    console.log('📝 Contenido procesado para cargar:', contentValue);
                    
                    // Establecer el valor en el campo hidden
                    if (contentField) {
                        contentField.value = contentValue;
                    }
                    
                    // PASO 4: Recargar formulario con el contenido procesado
                    setTimeout(() => {
                        if (window.jsonToContentArray) {
                            console.log('🔄 PASO 4: Aplicando contenido procesado al formulario...');
                            window.jsonToContentArray(contentValue);
                        } else if (window.contentCRUDManager) {
                            console.log('🔄 PASO 4: Usando ContentCRUDManager directamente...');
                            window.contentCRUDManager.loadFromJSON(contentValue);
                        }
                    }, 300);
                    
                    console.log('✅ Datos de lección cargados correctamente');
                }, 600); // Esperar más tiempo para asegurar limpieza completa
            }
        };
        
        // Función para obtener todos los datos del formulario
        window.getLessonFormData = function() {
            const formData = {
                course_id: document.getElementById('course_id').value,
                name: document.getElementById('name').value,
                level_number: document.getElementById('level_number').value,
                description: document.getElementById('description').value,
                difficulty: document.getElementById('difficulty').value,
                time_minutes: document.getElementById('time_minutes').value,
                content: document.getElementById('content').value
            };
            
            // Actualizar el contenido antes de retornar
            if (window.updateHiddenJson) {
                window.updateHiddenJson();
                formData.content = document.getElementById('content').value;
            }
            
            return formData;
        };

        // Función para cargar JSON de prueba o datos específicos
        window.loadTestContent = function() {
            const testJson = '[{"index": 0, "titulo": "ASD", "descripcion": "ASDAS", "contenido": "ASDASDASD", "media": {"tipo": "image", "url": "https://i.pinimg.com/474x/59/5e/ef/595eef2e2109829e64648b4438802849.jpg"}}]';
            
            const contentField = document.getElementById('content');
            if (contentField) {
                contentField.value = testJson;
                
                // Recargar el formulario de contenido con los nuevos datos
                if (window.jsonToContentArray) {
                    window.jsonToContentArray(testJson);
                }
                
                console.log('Datos de prueba cargados:', testJson);
                if (window.showToast) {
                    window.showToast('Datos de prueba cargados correctamente', 'success');
                }
            }
        };

        // Función para cargar JSON personalizado
        window.loadCustomContent = function(jsonString) {
            try {
                // Validar que sea un JSON válido
                JSON.parse(jsonString);
                
                const contentField = document.getElementById('content');
                if (contentField) {
                    contentField.value = jsonString;
                    
                    // Recargar el formulario de contenido con los nuevos datos
                    if (window.jsonToContentArray) {
                        window.jsonToContentArray(jsonString);
                    }
                    
                    console.log('Contenido personalizado cargado:', jsonString);
                    if (window.showToast) {
                        window.showToast('Contenido personalizado cargado correctamente', 'success');
                    }
                }
            } catch (error) {
                console.error('Error al cargar JSON:', error);
                if (window.showToast) {
                    window.showToast('Error: JSON no válido', 'error');
                }
            }
        };

        // Función para limpiar completamente el formulario de lección
        window.clearLessonForm = function() {
            console.log('🧹 Limpiando formulario de lección...');
            
            // Limpiar campos básicos
            document.getElementById('course_id').value = '';
            document.getElementById('name').value = '';
            document.getElementById('level_number').value = '';
            document.getElementById('description').value = '';
            document.getElementById('difficulty').value = '';
            document.getElementById('time_minutes').value = '';
            
            // Limpiar contenido
            const contentField = document.getElementById('content');
            if (contentField) {
                contentField.value = '[]';
                
                // Limpiar formulario de contenido usando el manager
                if (window.contentCRUDManager) {
                    window.contentCRUDManager.clear();
                } else if (window.clearContentForm) {
                    window.clearContentForm();
                }
            }
            
            console.log('✅ Formulario de lección limpiado');
        };

        // Función para reinicializar completamente el formulario
        window.reinitializeLessonForm = function() {
            console.log('🔄 Reinicializando formulario de lección...');
            
            // Limpiar todo primero
            window.clearLessonForm();
            
            // Esperar y reinicializar el formulario de contenido
            setTimeout(() => {
                if (window.contentCRUDManager) {
                    console.log('🔄 Forzando reinicialización con ContentCRUDManager...');
                    window.contentCRUDManager.clear();
                } else if (window.jsonToContentArray) {
                    console.log('🔄 Forzando reinicialización del formulario de contenido...');
                    window.jsonToContentArray('[]');
                }
            }, 500);
        };

        // Función específica para mostrar datos de un registro (show/view)
        window.showLessonData = function(lessonData) {
            console.log('👀 Mostrando datos de lección para visualización:', lessonData);
            
            if (lessonData) {
                // Cargar datos básicos inmediatamente
                document.getElementById('course_id').value = lessonData.course_id || '';
                document.getElementById('name').value = lessonData.name || '';
                document.getElementById('level_number').value = lessonData.level_number || '';
                document.getElementById('description').value = lessonData.description || '';
                document.getElementById('difficulty').value = lessonData.difficulty || '';
                document.getElementById('time_minutes').value = lessonData.time_minutes || '';
                
                // Procesar y mostrar contenido
                const contentField = document.getElementById('content');
                let contentForDisplay = '[]';
                
                if (lessonData.content !== null && lessonData.content !== undefined) {
                    if (typeof lessonData.content === 'string') {
                        contentForDisplay = lessonData.content;
                    } else {
                        contentForDisplay = JSON.stringify(lessonData.content);
                    }
                }
                
                console.log('📋 Contenido para mostrar:', contentForDisplay);
                
                if (contentField) {
                    contentField.value = contentForDisplay;
                }
                
                // Renderizar el contenido en el formulario
                if (window.jsonToContentArray) {
                    window.jsonToContentArray(contentForDisplay);
                }
                
                console.log('✅ Datos de lección mostrados correctamente');
            }
        };

        // Función de debugging para analizar la respuesta del API
        window.debugApiResponse = function(apiResponse) {
            console.log('🔍 DEBUG: Respuesta completa del API:', apiResponse);
            
            if (apiResponse && apiResponse.data) {
                if (Array.isArray(apiResponse.data)) {
                    console.log('📋 Lista de lecciones encontradas:', apiResponse.data.length);
                    apiResponse.data.forEach((lesson, index) => {
                        console.log(`📖 Lección ${index + 1}:`, {
                            id: lesson.id,
                            name: lesson.name,
                            content_type: typeof lesson.content,
                            content_is_null: lesson.content === null,
                            content_length: Array.isArray(lesson.content) ? lesson.content.length : 'N/A',
                            content_preview: lesson.content
                        });
                    });
                } else {
                    console.log('📖 Lección individual:', {
                        id: apiResponse.data.id,
                        name: apiResponse.data.name,
                        content_type: typeof apiResponse.data.content,
                        content_is_null: apiResponse.data.content === null,
                        content_length: Array.isArray(apiResponse.data.content) ? apiResponse.data.content.length : 'N/A',
                        content_preview: apiResponse.data.content
                    });
                }
            }
        };
        
        // =============================
        // FUNCIONES DE DEBUGGING
        // =============================
        
        window.debugContentForm = function() {
            console.log('🔍 DEBUG - Estado del formulario de contenido:');
            
            const contentField = document.getElementById('content');
            const container = document.getElementById('content-items-container');
            
            console.log('📋 Campo hidden value:', contentField ? contentField.value : 'No encontrado');
            console.log('📦 Contenedor items:', container ? container.children.length + ' elementos' : 'No encontrado');
            
            if (window.contentCRUDManager) {
                const stats = window.contentCRUDManager.getStats();
                console.log('📊 Estadísticas del manager:', stats);
                
                const data = window.contentCRUDManager.getData();
                console.log('📝 Datos actuales:', data);
            }
            
            if (window.showToast) {
                window.showToast('Debug info logged to console', 'info');
            }
        };
        
        window.loadTestContentData = function() {
            const testData = [
                {
                    "index": 0,
                    "titulo": "Prueba Debug 1",
                    "descripcion": "Esta es una descripción de prueba para debugging",
                    "contenido": "Contenido de prueba para verificar que el CRUD funciona correctamente",
                    "media": {
                        "tipo": "image",
                        "url": "https://picsum.photos/400/300?random=1"
                    }
                },
                {
                    "index": 1,
                    "titulo": "Prueba Debug 2",
                    "descripcion": "Segunda descripción de prueba",
                    "contenido": "Segundo contenido de prueba con video de YouTube",
                    "media": {
                        "tipo": "video",
                        "url": "https://www.youtube.com/watch?v=dQw4w9WgXcQ"
                    }
                }
            ];
            
            const jsonString = JSON.stringify(testData);
            
            if (window.contentCRUDManager) {
                window.contentCRUDManager.loadFromJSON(jsonString);
            } else if (window.jsonToContentArray) {
                window.jsonToContentArray(jsonString);
            }
            
            console.log('🧪 Datos de prueba cargados:', testData);
            
            if (window.showToast) {
                window.showToast('Datos de prueba cargados correctamente', 'success');
            }
        };
    });
</script>