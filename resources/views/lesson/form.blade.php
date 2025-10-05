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
        <input type="hidden" name="content" id="content" value='[{"index": 0, "titulo": "ASD", "descripcion": "ASDAS", "contenido": "ASDASDASD", "media": {"tipo": "image", "url": "https://i.pinimg.com/474x/59/5e/ef/595eef2e2109829e64648b4438802849.jpg"}}]'>
        <!-- Include content form -->
        @include('content.form', ['contentJson' => old('content', '[{"index": 0, "titulo": "ASD", "descripcion": "ASDAS", "contenido": "ASDASDASD", "media": {"tipo": "image", "url": "https://i.pinimg.com/474x/59/5e/ef/595eef2e2109829e64648b4438802849.jpg"}}]')])
        <div class="invalid-feedback"></div>
    </div>



    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Guardar lección</button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
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
                
                // Cargar contenido dinámico
                const contentField = document.getElementById('content');
                if (lessonData.content && contentField) {
                    // Verificar si el contenido es un string JSON o un objeto
                    let contentValue;
                    if (typeof lessonData.content === 'string') {
                        contentValue = lessonData.content;
                    } else {
                        contentValue = JSON.stringify(lessonData.content);
                    }
                    
                    contentField.value = contentValue;
                    
                    // Esperar a que el formulario de contenido esté disponible
                    setTimeout(() => {
                        if (window.jsonToContentArray) {
                            window.jsonToContentArray(contentValue);
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
    });
</script>