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

    @php
        // Definir el JSON por defecto simple para evitar problemas de sintaxis en Blade
        $defaultContentJson = '[]';
        $contentValue = old('content', $defaultContentJson);
    @endphp

    <!-- Content -->
    <div class="mb-3">
        <label class="form-label">Contenido de la lección *</label>
        <input type="hidden" name="content" id="content" value="{{ htmlspecialchars($contentValue, ENT_QUOTES, 'UTF-8') }}">
        <!-- Include content form - pasamos array vacío para evitar errores de Blade -->
        @include('content.form')
        <div class="invalid-feedback"></div>
    </div>



    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Guardar lección</button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Función para limpiar el formulario de lección
        window.clearLessonForm = function() {
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
            }
            
            // Limpiar formulario de contenido si existe la función
            if (window.clearContentForm) {
                window.clearContentForm();
            }
            
            // Remover clases de validación
            document.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
            
            // Limpiar mensajes de error
            document.querySelectorAll('.invalid-feedback').forEach(el => {
                el.textContent = '';
            });
            
            // Remover ID de lección del formulario (para modo creación)
            const form = document.getElementById('lessonForm');
            if (form) {
                delete form.dataset.lessonId;
            }
            
            console.log('Formulario de lección limpiado');
        };

        // Función para cargar datos de la lección al editar
        window.loadLessonData = function(lessonData) {
            if (lessonData) {
                // Cargar datos básicos del formulario
                document.getElementById('course_id').value = lessonData.course_id || '';
                document.getElementById('name').value = lessonData.name || '';
                document.getElementById('level_number').value = lessonData.level_number || '';
                document.getElementById('description').value = lessonData.description || '';
                document.getElementById('difficulty').value = lessonData.difficulty || '';
                document.getElementById('time_minutes').value = lessonData.time_minutes || '';
                
                // Cargar contenido dinámico de la lección
                if (lessonData.content) {
                    console.log('=== PROCESANDO CONTENIDO DE LECCIÓN ===');
                    console.log('Contenido completo recibido:', lessonData.content);
                    console.log('Tipo de contenido:', typeof lessonData.content);
                    console.log('Es array:', Array.isArray(lessonData.content));
                    console.log('Cantidad de elementos:', Array.isArray(lessonData.content) ? lessonData.content.length : 'N/A');
                    
                    let contentValue;
                    
                    // El endpoint devuelve content como array de objetos
                    if (Array.isArray(lessonData.content) && lessonData.content.length > 0) {
                        // Es un array con elementos (caso normal del endpoint)
                        contentValue = JSON.stringify(lessonData.content);
                        console.log('✅ Array de contenido convertido a JSON:');
                        console.log('Elementos encontrados:');
                        lessonData.content.forEach((item, index) => {
                            console.log(`  ${index + 1}. "${item.titulo}" (${item.media?.tipo})`);
                        });
                        console.log('JSON final:', contentValue);
                    } else if (Array.isArray(lessonData.content) && lessonData.content.length === 0) {
                        // Es un array vacío
                        contentValue = '[]';
                        console.log('⚠️ Array de contenido vacío');
                    } else if (typeof lessonData.content === 'string') {
                        // Es un string JSON (menos común)
                        contentValue = lessonData.content;
                        console.log('📝 Contenido ya es string JSON:', contentValue);
                    } else if (typeof lessonData.content === 'object') {
                        // Es un objeto, convertir a JSON
                        contentValue = JSON.stringify(lessonData.content);
                        console.log('🔄 Objeto convertido a JSON:', contentValue);
                    } else {
                        // Fallback: crear array vacío
                        contentValue = '[]';
                        console.log('❌ Contenido no válido, usando array vacío');
                    }
                    
                    // Actualizar el campo hidden
                    const contentField = document.getElementById('content');
                    if (contentField) {
                        contentField.value = contentValue;
                        console.log('Campo hidden actualizado con:', contentValue);
                    }
                    
                    // Usar la función específica para procesar contenido del endpoint
                    setTimeout(() => {
                        if (window.processEndpointContent && Array.isArray(lessonData.content)) {
                            // Procesar directamente el array del endpoint (método preferido)
                            console.log('🎯 Usando processEndpointContent para procesar array del endpoint...');
                            window.processEndpointContent(lessonData.content);
                        } else if (window.loadLessonContent) {
                            // Función que limpia completamente y carga el nuevo contenido (fallback)
                            console.log('🔄 Usando loadLessonContent con JSON string...');
                            window.loadLessonContent(contentValue);
                        } else if (window.jsonToContentArray) {
                            // Fallback final a la función original
                            console.log('⚠️ Usando fallback jsonToContentArray...');
                            window.jsonToContentArray(contentValue);
                        } else {
                            console.error('❌ No se encontraron funciones de carga de contenido');
                        }
                    }, 150);

        // Función específica para procesar contenido del endpoint
        window.processEndpointContent = function(contentArray) {
            console.log('=== PROCESANDO CONTENIDO DEL ENDPOINT ===');
            console.log('Array recibido:', contentArray);
            
            if (!Array.isArray(contentArray)) {
                console.error('❌ El contenido no es un array válido');
                return;
            }
            
            // Limpiar el formulario de contenido existente
            if (window.clearContentForm) {
                window.clearContentForm();
            }
            
            // Si no hay contenido, mostrar un elemento vacío
            if (contentArray.length === 0) {
                console.log('📝 Array vacío, mostrando formulario limpio');
                return;
            }
            
            // Procesar cada elemento del contenido
            contentArray.forEach((item, index) => {
                console.log(`📋 Procesando elemento ${index + 1}:`, item);
                
                // Agregar nuevo elemento al formulario si no es el primero
                if (index > 0 && window.addContentItem) {
                    window.addContentItem();
                }
                
                // Llenar los campos con los datos del endpoint
                setTimeout(() => {
                    // Título
                    const titleField = document.querySelector(`[name="content_items[${index}][titulo]"]`);
                    if (titleField && item.titulo) {
                        titleField.value = item.titulo;
                        console.log(`✅ Título ${index}: ${item.titulo}`);
                    }
                    
                    // Descripción
                    const descField = document.querySelector(`[name="content_items[${index}][descripcion]"]`);
                    if (descField && item.descripcion) {
                        descField.value = item.descripcion;
                        console.log(`✅ Descripción ${index}: ${item.descripcion}`);
                    }
                    
                    // Contenido
                    const contentField = document.querySelector(`[name="content_items[${index}][contenido]"]`);
                    if (contentField && item.contenido) {
                        contentField.value = item.contenido;
                        console.log(`✅ Contenido ${index}: ${item.contenido.substring(0, 50)}...`);
                    }
                    
                    // Media - tipo
                    if (item.media && item.media.tipo) {
                        const mediaTypeField = document.querySelector(`[name="content_items[${index}][media_tipo]"]`);
                        if (mediaTypeField) {
                            mediaTypeField.value = item.media.tipo;
                            console.log(`✅ Tipo de media ${index}: ${item.media.tipo}`);
                            
                            // Disparar evento change para actualizar la UI
                            mediaTypeField.dispatchEvent(new Event('change'));
                        }
                    }
                    
                    // Media - URL
                    if (item.media && item.media.url) {
                        const mediaUrlField = document.querySelector(`[name="content_items[${index}][media_url]"]`);
                        if (mediaUrlField) {
                            mediaUrlField.value = item.media.url;
                            console.log(`✅ URL de media ${index}: ${item.media.url}`);
                        }
                    }
                    
                    // Actualizar preview si existe la función
                    if (window.updateMediaPreview) {
                        window.updateMediaPreview(index);
                    }
                    
                }, (index * 100) + 50); // Escalonar las asignaciones para evitar conflictos
            });
            
            // Actualizar el JSON hidden después de cargar todo
            setTimeout(() => {
                if (window.updateHiddenJson) {
                    window.updateHiddenJson();
                    console.log('✅ JSON hidden actualizado después de cargar endpoint content');
                }
            }, (contentArray.length * 100) + 200);
            
            console.log(`✅ Procesamiento completado: ${contentArray.length} elementos cargados`);
        };
                } else {
                    // Si no hay contenido, limpiar el formulario
                    console.log('No hay contenido, limpiando formulario...');
                    setTimeout(() => {
                        if (window.clearContentForm) {
                            window.clearContentForm();
                        }
                    }, 100);
                }
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

        // Inicializar contenido por defecto si hay datos en el campo hidden
        setTimeout(() => {
            const contentField = document.getElementById('content');
            if (contentField && contentField.value && contentField.value !== '[]') {
                console.log('Inicializando contenido desde campo hidden:', contentField.value);
                
                // Si hay contenido, cargarlo
                if (window.jsonToContentArray) {
                    window.jsonToContentArray(contentField.value);
                }
            } else {
                console.log('No hay contenido inicial, usando formulario vacío');
                
                // Si no hay contenido, asegurar que hay al menos un item vacío
                if (window.clearContentForm) {
                    window.clearContentForm();
                }
            }
        }, 200);
    });
</script>