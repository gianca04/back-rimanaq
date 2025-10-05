@extends('layouts.app')

@section('title', 'Crear Gesto')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Crear Nuevo Gesto</h3>
        <a href="{{ route('web.gestures.index') }}" class="btn btn-secondary">Volver a Gestos</a>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Información del Gesto</h5>
        </div>
        <div class="card-body">
            @include('gesture.form')
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="module">
    import routes from '/js/routes.js';
    
    const gestureForm = document.getElementById('gestureForm');
    const courseSelect = document.getElementById('course_id');
    const lessonSelect = document.getElementById('lesson_id');
    const token = localStorage.getItem('auth_token');

    // Cargar cursos al inicializar la página
    async function loadCourses() {
        // Mostrar estado de carga inicial
        courseSelect.innerHTML = '<option value="">⏳ Cargando cursos...</option>';
        courseSelect.disabled = true;
        
        try {
            const response = await fetch('/api/courses', {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                const result = await response.json();
                const courses = result.data || result; // Manejar ambos formatos de respuesta
                
                courseSelect.innerHTML = '<option value="">Seleccionar curso...</option>';
                courseSelect.disabled = false;
                
                if (Array.isArray(courses) && courses.length > 0) {
                    // Ordenar cursos por nombre
                    courses.sort((a, b) => a.name.localeCompare(b.name));
                    
                    courses.forEach(course => {
                        const option = document.createElement('option');
                        option.value = course.id;
                        option.textContent = course.name;
                        option.title = course.description; // Mostrar descripción en tooltip
                        courseSelect.appendChild(option);
                    });
                    
                    // Actualizar texto de ayuda
                    updateCourseHelp(`${courses.length} curso(s) disponible(s).`);
                } else {
                    courseSelect.innerHTML = '<option value="">No hay cursos disponibles</option>';
                    courseSelect.disabled = true;
                    updateCourseHelp('No hay cursos disponibles. Crea un curso primero.');
                }
            } else {
                console.error('Error al cargar cursos:', response.status);
                courseSelect.innerHTML = '<option value="">❌ Error al cargar cursos</option>';
                courseSelect.disabled = true;
                updateCourseHelp('Error al cargar cursos. Por favor, recarga la página.');
            }
        } catch (error) {
            console.error('Error:', error);
            courseSelect.innerHTML = '<option value="">❌ Error de conexión</option>';
            courseSelect.disabled = true;
            updateCourseHelp('Error de conexión. Verifica tu conexión e intenta de nuevo.');
        }
    }

    // Cargar lecciones cuando se selecciona un curso
    courseSelect.addEventListener('change', async function() {
        const courseId = this.value;
        lessonSelect.innerHTML = '<option value="">Seleccionar lección...</option>';
        lessonSelect.disabled = !courseId;
        
        if (!courseId) {
            updateLessonHelp('Primero selecciona un curso para ver las lecciones disponibles.');
            return;
        }

        // Mostrar loading con spinner
        lessonSelect.innerHTML = '<option value="">⏳ Cargando lecciones...</option>';
        lessonSelect.disabled = true;
        updateLessonHelp('🔄 Cargando lecciones del curso seleccionado...');

        try {
            const response = await fetch(`/api/courses/${courseId}/lessons`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                const result = await response.json();
                const lessons = result.data || result;
                
                lessonSelect.innerHTML = '<option value="">Seleccionar lección...</option>';
                
                if (!Array.isArray(lessons) || lessons.length === 0) {
                    lessonSelect.innerHTML = '<option value="">No hay lecciones disponibles</option>';
                    lessonSelect.disabled = true;
                    updateLessonHelp('No hay lecciones disponibles en este curso.');
                } else {
                    // Ordenar lecciones por level_number
                    lessons.sort((a, b) => a.level_number - b.level_number);
                    
                    lessons.forEach(lesson => {
                        const option = document.createElement('option');
                        option.value = lesson.id;
                        option.textContent = `Nivel ${lesson.level_number}: ${lesson.name}`;
                        // Agregar información adicional en el title
                        option.title = `${lesson.description} (${lesson.difficulty}, ${lesson.time_minutes} min)`;
                        lessonSelect.appendChild(option);
                    });
                    
                    lessonSelect.disabled = false;
                    updateLessonHelp(`${lessons.length} lección(es) disponible(s) en este curso.`);
                }
            } else {
                const errorData = await response.json().catch(() => ({}));
                console.error('Error al cargar lecciones:', response.status, errorData);
                lessonSelect.innerHTML = '<option value="">Error al cargar lecciones</option>';
                lessonSelect.disabled = true;
                updateLessonHelp('Error al cargar las lecciones. Por favor, intenta de nuevo.');
            }
        } catch (error) {
            console.error('Error:', error);
            lessonSelect.innerHTML = '<option value="">Error de conexión</option>';
            lessonSelect.disabled = true;
            updateLessonHelp('Error de conexión al cargar las lecciones.');
        }
    });

    // Manejar envío del formulario
    gestureForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Limpiar errores previos
        clearValidationErrors();

        // Validar formulario
        if (!validateForm()) {
            return;
        }

        // Deshabilitar botón de envío para evitar doble envío
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Guardando...';

        try {
            // Parsear frames
            const frames = JSON.parse(gestureForm.frames.value);

            // Preparar gesture_data según la estructura esperada por la API
            const gestureData = {
                id: Date.now(), // ID único basado en timestamp
                name: gestureForm.gesture_name.value.toUpperCase(),
                frames: frames,
                frameCount: frames.length,
                isSequential: gestureForm.isSequential.value === 'true'
            };

            const formData = {
                lesson_id: parseInt(gestureForm.lesson_id.value),
                gesture_data: gestureData
            };

            const response = await fetch('/api/gestures', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json();

            if (response.ok) {
                // Mostrar mensaje de éxito
                submitButton.innerHTML = '<i class="fas fa-check me-1"></i>¡Guardado!';
                submitButton.classList.remove('btn-primary');
                submitButton.classList.add('btn-success');
                
                setTimeout(() => {
                    window.location.href = routes.gestures || '/dashboard/gestures';
                }, 1500);
            } else {
                if (result.errors) {
                    // Mostrar errores de validación
                    Object.keys(result.errors).forEach(field => {
                        showFieldError(field, result.errors[field][0]);
                    });
                } else {
                    alert(result.message || 'Error al crear el gesto');
                }
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al crear el gesto 221: ' + error.message);
        } finally {
            // Restaurar botón de envío
            setTimeout(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
                submitButton.classList.remove('btn-success');
                submitButton.classList.add('btn-primary');
            }, 2000);
        }
    });

    // Funciones auxiliares para manejo de errores
    function clearValidationErrors() {
        document.querySelectorAll('.is-invalid').forEach(input => {
            input.classList.remove('is-invalid');
        });
        document.querySelectorAll('.invalid-feedback').forEach(feedback => {
            feedback.textContent = '';
        });
    }

    function showFieldError(fieldName, message) {
        const field = document.querySelector(`[name="${fieldName}"]`);
        const feedback = field.parentNode.querySelector('.invalid-feedback');
        
        if (field && feedback) {
            field.classList.add('is-invalid');
            feedback.textContent = message;
        }
    }

    function updateLessonHelp(message) {
        const helpText = lessonSelect.parentNode.querySelector('.form-text');
        if (helpText) {
            helpText.textContent = message;
        }
    }

    function updateCourseHelp(message) {
        const helpText = courseSelect.parentNode.querySelector('.form-text');
        if (helpText) {
            helpText.textContent = message;
        }
    }

    // Convertir nombre a mayúsculas automáticamente
    document.getElementById('gesture_name').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });

    // Validaciones adicionales antes del envío
    function validateForm() {
        let isValid = true;
        
        // Validar curso seleccionado
        if (!courseSelect.value) {
            showFieldError('course_id', 'Debes seleccionar un curso');
            isValid = false;
        }
        
        // Validar lección seleccionada
        if (!lessonSelect.value) {
            showFieldError('lesson_id', 'Debes seleccionar una lección');
            isValid = false;
        }
        
        // Validar nombre del gesto
        const gestureName = gestureForm.gesture_name.value.trim();
        if (!gestureName) {
            showFieldError('gesture_name', 'El nombre del gesto es obligatorio');
            isValid = false;
        } else if (gestureName.length < 2) {
            showFieldError('gesture_name', 'El nombre debe tener al menos 2 caracteres');
            isValid = false;
        }
        
        return isValid;
    }

    // Cargar cursos al inicializar
    loadCourses();
</script>
@endsection