<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lecciones del Curso - Rimanaq</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- jTable CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jtable/2.5.0/themes/lightcolor/blue/jtable.min.css">
    
    <!-- jQuery UI CSS -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/ui-lightness/jquery-ui.css">
    
    <style>
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        .main-container {
            margin-top: 20px;
        }
        .active {
            font-weight: bold;
        }
        .breadcrumb {
            background-color: #f8f9fa;
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .course-info {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #2196f3;
        }
        .debug-info {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 15px;
            font-family: monospace;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/dashboard">
                <i class="fas fa-graduation-cap me-2"></i>
                Rimanaq Admin
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard">
                            <i class="fas fa-tachometer-alt me-1"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard/courses">
                            <i class="fas fa-book me-1"></i>
                            Cursos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/dashboard/lessons">
                            <i class="fas fa-list-alt me-1"></i>
                            Lecciones
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard/gestures">
                            <i class="fas fa-hand-paper me-1"></i>
                            Gestos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard/progress">
                            <i class="fas fa-chart-line me-1"></i>
                            Progreso
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <button class="btn btn-outline-light" onclick="logout()">
                            <i class="fas fa-sign-out-alt me-1"></i>
                            Cerrar Sesión
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid main-container">
        <div class="row">
            <div class="col-12">
                <!-- Breadcrumbs -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="/dashboard/courses">Cursos</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Lecciones</li>
                    </ol>
                </nav>

                <!-- Debug Info -->
                <div class="debug-info" id="debugInfo">
                    <strong>Debug Information:</strong><br>
                    URL: <span id="debugUrl"></span><br>
                    Course ID: <span id="debugCourseId"></span><br>
                    Course Name: <span id="debugCourseName"></span><br>
                    Path Parts: <span id="debugPathParts"></span>
                </div>

                <!-- Información del curso -->
                <div class="course-info">
                    <h4 id="course-title">Cargando información del curso...</h4>
                    <p class="mb-0">Gestiona las lecciones de este curso</p>
                </div>
                
                <div id="LessonsTableContainer" style="margin-top: 20px;"></div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jtable/2.5.0/jquery.jtable.min.js"></script>

    <script>
        // Variables globales
        let courseId = null;
        let courseName = '';

        // Configurar CSRF token para todas las peticiones
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token')
            }
        });

        // Función para actualizar debug info
        function updateDebugInfo() {
            $('#debugUrl').text(window.location.href);
            $('#debugCourseId').text(courseId || 'null');
            $('#debugCourseName').text(courseName || 'empty');
            $('#debugPathParts').text(JSON.stringify(window.location.pathname.split('/')));
        }

        // Verificación simplificada de jTable
        function waitForJTable() {
            if (typeof $.fn.jtable !== 'undefined') {
                console.log('✅ jTable disponible, inicializando...');
                initializeApp();
            } else {
                console.log('⏳ Esperando jTable...');
                setTimeout(waitForJTable, 500);
            }
        }

        function initializeApp() {
            console.log('🚀 Inicializando aplicación...');
            
            // Verificar autenticación
            const token = localStorage.getItem('auth_token');
            if (!token) {
                console.error('❌ No hay token de autenticación');
                alert('Sesión expirada. Redirigiendo al login...');
                window.location.href = '/';
                return;
            }

            // Obtener ID del curso desde la URL
            const pathParts = window.location.pathname.split('/');
            console.log('🔍 Partes de la URL:', pathParts);
            console.log('🔍 URL completa:', window.location.pathname);
            
            // Intentar diferentes posiciones en el path
            let possibleCourseId = null;
            
            // /dashboard/courses/{id}/lessons
            if (pathParts.length >= 4 && pathParts[2] === 'courses') {
                possibleCourseId = pathParts[3];
                console.log('📍 Detectado patrón /dashboard/courses/{id}/lessons');
            }
            
            console.log('🔍 ID posible del curso:', possibleCourseId);
            
            // Validar que courseId es un número válido
            if (!possibleCourseId || isNaN(possibleCourseId)) {
                console.error('❌ ID del curso no válido:', possibleCourseId);
                alert('Error: ID del curso no válido en la URL.\nURL esperada: /dashboard/courses/{ID}/lessons');
                window.location.href = '/dashboard/courses';
                return;
            }
            
            courseId = parseInt(possibleCourseId);
            console.log('✅ Course ID parseado:', courseId);

            // Obtener nombre del curso desde los parámetros de la URL
            const urlParams = new URLSearchParams(window.location.search);
            courseName = urlParams.get('courseName') || 'Curso';
            console.log('📝 Nombre del curso:', courseName);

            // Actualizar título e info de debug
            document.getElementById('course-title').textContent = 'Lecciones de: ' + courseName;
            updateDebugInfo();

            console.log('🏗️ Inicializando tabla para curso ID:', courseId);
            initializeLessonsTable();
        }

        function initializeLessonsTable() {
            console.log('📊 Configurando jTable...');
            
            // Configuración de jTable
            const tableConfig = {
                title: 'Lecciones del Curso: ' + courseName,
                paging: true,
                pageSize: 10,
                sorting: true,
                defaultSorting: 'level_number ASC',
                
                actions: {
                    listAction: function (postData) {
                        console.log('📥 ListAction - Cargando lecciones para curso:', courseId);
                        
                        return $.ajax({
                            url: '/api/courses/' + courseId + '/lessons',
                            type: 'GET',
                            dataType: 'json',
                        }).then(function(response) {
                            console.log('✅ Lecciones cargadas:', response);
                            return {
                                Result: 'OK',
                                Records: response.data || [],
                                TotalRecordCount: (response.data || []).length
                            };
                        }).catch(function(xhr) {
                            console.error('❌ Error cargando lecciones:', xhr);
                            const message = xhr.responseJSON?.message || xhr.statusText;
                            alert('Error al cargar lecciones: ' + message);
                            return {
                                Result: 'ERROR',
                                Message: 'Error al cargar lecciones: ' + message
                            };
                        });
                    },
                    
                    createAction: function (postData) {
                        console.log('=== 📝 CREATE ACTION DEBUG ===');
                        console.log('🔍 courseId variable:', courseId);
                        console.log('🔍 postData antes:', JSON.stringify(postData, null, 2));
                        
                        // Validar courseId ANTES de enviarlo
                        if (!courseId || courseId === 'undefined' || courseId === null || isNaN(courseId)) {
                            console.error('❌ courseId inválido:', courseId);
                            alert('Error: ID del curso no válido');
                            return Promise.resolve({
                                Result: 'ERROR',
                                Message: 'Error: ID del curso no válido'
                            });
                        }
                        
                        // Asegurar que course_id esté presente y sea correcto
                        postData.course_id = parseInt(courseId);
                        
                        // Validar otros campos requeridos
                        if (!postData.name || postData.name.trim() === '') {
                            alert('El nombre de la lección es requerido');
                            return Promise.resolve({
                                Result: 'ERROR',
                                Message: 'El nombre de la lección es requerido'
                            });
                        }
                        
                        if (!postData.description || postData.description.trim() === '') {
                            alert('La descripción de la lección es requerida');
                            return Promise.resolve({
                                Result: 'ERROR',
                                Message: 'La descripción de la lección es requerida'
                            });
                        }
                        
                        // Convertir números
                        postData.level_number = parseInt(postData.level_number) || 1;
                        postData.time_minutes = parseInt(postData.time_minutes) || 30;
                        
                        console.log('✅ postData después:', JSON.stringify(postData, null, 2));
                        console.log('=== 📤 ENVIANDO PETICIÓN ===');
                        
                        return $.ajax({
                            url: '/api/lessons',
                            type: 'POST',
                            dataType: 'json',
                            data: postData,
                            contentType: 'application/x-www-form-urlencoded',
                            beforeSend: function() {
                                console.log('📤 Enviando datos:', postData);
                            }
                        }).then(function(response) {
                            console.log('✅ Lección creada exitosamente:', response);
                            return {
                                Result: 'OK',
                                Record: response.data
                            };
                        }).catch(function(xhr) {
                            console.error('❌ Error creando lección:', xhr);
                            console.error('📄 Response completo:', xhr.responseJSON);
                            
                            let errorMessage = 'Error al crear lección';
                            
                            if (xhr.responseJSON) {
                                if (xhr.responseJSON.errors) {
                                    // Mostrar errores de validación específicos
                                    console.error('❌ Errores de validación:', xhr.responseJSON.errors);
                                    const errorsList = Object.entries(xhr.responseJSON.errors).map(([field, messages]) => {
                                        return `${field}: ${messages.join(', ')}`;
                                    });
                                    errorMessage = 'Errores de validación:\n' + errorsList.join('\n');
                                    alert(errorMessage);
                                } else if (xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                    alert(errorMessage);
                                }
                            } else {
                                alert('Error de conexión o servidor');
                            }
                            
                            return {
                                Result: 'ERROR',
                                Message: errorMessage
                            };
                        });
                    },
                    
                    updateAction: function (postData) {
                        console.log('📝 Actualizando lección:', postData);
                        
                        // Asegurar que el course_id esté presente
                        if (!postData.course_id) {
                            postData.course_id = courseId;
                        }
                        
                        // Convertir números si están presentes
                        if (postData.course_id) postData.course_id = parseInt(postData.course_id);
                        if (postData.level_number) postData.level_number = parseInt(postData.level_number);
                        if (postData.time_minutes) postData.time_minutes = parseInt(postData.time_minutes);
                        
                        return $.ajax({
                            url: '/api/lessons/' + postData.id,
                            type: 'PUT',
                            dataType: 'json',
                            data: postData,
                            contentType: 'application/x-www-form-urlencoded'
                        }).then(function(response) {
                            console.log('✅ Lección actualizada:', response);
                            return {
                                Result: 'OK',
                                Record: response.data
                            };
                        }).catch(function(xhr) {
                            console.error('❌ Error actualizando lección:', xhr);
                            let errorMessage = 'Error al actualizar lección';
                            
                            if (xhr.responseJSON?.errors) {
                                const errorsList = Object.values(xhr.responseJSON.errors).flat();
                                errorMessage = 'Errores: ' + errorsList.join(', ');
                            } else if (xhr.responseJSON?.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            
                            return {
                                Result: 'ERROR',
                                Message: errorMessage
                            };
                        });
                    },
                    
                    deleteAction: function (postData) {
                        console.log('🗑️ Eliminando lección:', postData.id);
                        
                        return $.ajax({
                            url: '/api/lessons/' + postData.id,
                            type: 'DELETE',
                            dataType: 'json'
                        }).then(function(response) {
                            console.log('✅ Lección eliminada:', response);
                            return {
                                Result: 'OK'
                            };
                        }).catch(function(xhr) {
                            console.error('❌ Error eliminando lección:', xhr);
                            let errorMessage = 'Error al eliminar lección';
                            
                            if (xhr.responseJSON?.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            
                            return {
                                Result: 'ERROR',
                                Message: errorMessage
                            };
                        });
                    }
                },
                
                fields: {
                    id: {
                        key: true,
                        create: false,
                        edit: false,
                        list: true,
                        title: 'ID',
                        width: '5%'
                    },
                    course_id: {
                        type: 'hidden',
                        defaultValue: function() { 
                            console.log('🔧 Estableciendo defaultValue para course_id:', courseId);
                            return courseId; 
                        },
                        create: true,
                        edit: false, // No editable en updates
                        list: false
                    },
                    name: {
                        title: 'Nombre',
                        width: '25%',
                        inputClass: 'form-control',
                        inputTitle: 'Nombre de la lección (requerido)'
                    },
                    description: {
                        title: 'Descripción',
                        width: '30%',
                        type: 'textarea',
                        inputClass: 'form-control',
                        inputTitle: 'Descripción de la lección (requerido)'
                    },
                    level_number: {
                        title: 'Nivel',
                        width: '10%',
                        inputClass: 'form-control',
                        type: 'number',
                        defaultValue: 1,
                        inputTitle: 'Número del nivel (1-100)'
                    },
                    difficulty: {
                        title: 'Dificultad',
                        width: '12%',
                        type: 'option',
                        options: {
                            'fácil': 'Fácil',
                            'intermedio': 'Intermedio',
                            'difícil': 'Difícil'
                        },
                        defaultValue: 'fácil',
                        inputClass: 'form-select'
                    },
                    time_minutes: {
                        title: 'Tiempo (min)',
                        width: '10%',
                        inputClass: 'form-control',
                        type: 'number',
                        defaultValue: 30,
                        inputTitle: 'Duración en minutos (5-600)'
                    },
                    Actions: {
                        title: 'Gestos',
                        width: '8%',
                        sorting: false,
                        edit: false,
                        create: false,
                        display: function (data) {
                            return `<button type="button" class="btn btn-sm btn-success" onclick="viewLessonGestures(${data.record.id}, '${data.record.name.replace(/'/g, "\\'")}')">
                                        <i class="fas fa-hand-paper me-1"></i>Ver
                                    </button>`;
                        }
                    }
                }
            };

            // Inicializar jTable
            $('#LessonsTableContainer').jtable(tableConfig);

            // Event handlers
            $('#LessonsTableContainer').on('formCreated', function (event, data) {
                console.log('📝 Formulario creado:', data.formType);
                
                if (data.formType === 'create') {
                    // Asegurar que el course_id esté presente y sea correcto
                    setTimeout(function() {
                        const courseIdInput = data.form.find('input[name="course_id"]');
                        if (courseIdInput.length > 0) {
                            courseIdInput.val(courseId);
                            console.log('🔧 course_id establecido en campo oculto:', courseId);
                        } else {
                            console.warn('⚠️ Campo oculto course_id no encontrado');
                        }
                        
                        // Debug: mostrar todos los campos del formulario
                        console.log('🔍 Campos del formulario:');
                        data.form.find('input, select, textarea').each(function() {
                            console.log(`  - ${this.name}: ${this.value} (type: ${this.type})`);
                        });
                    }, 100);
                }
            });

            $('#LessonsTableContainer').on('recordAdded', function (event, data) {
                console.log('✅ Lección agregada exitosamente:', data.record);
                alert('Lección creada exitosamente');
            });

            $('#LessonsTableContainer').on('recordUpdated', function (event, data) {
                console.log('✅ Lección actualizada exitosamente:', data.record);
                alert('Lección actualizada exitosamente');
            });

            $('#LessonsTableContainer').on('recordDeleted', function (event, data) {
                console.log('✅ Lección eliminada exitosamente');
                alert('Lección eliminada exitosamente');
            });

            // Cargar datos
            console.log('📊 Cargando datos de la tabla...');
            $('#LessonsTableContainer').jtable('load');
        }

        // Función para ver gestos de una lección
        function viewLessonGestures(lessonId, lessonName) {
            const encodedName = encodeURIComponent(lessonName);
            const encodedCourseName = encodeURIComponent(courseName);
            const url = `/dashboard/lessons/${lessonId}/gestures?lessonName=${encodedName}&courseId=${courseId}&courseName=${encodedCourseName}`;
            console.log('🔗 Navegando a gestos:', url);
            window.location.href = url;
        }

        // Inicializar cuando esté listo
        $(document).ready(function() {
            console.log('📄 DOM listo, esperando jTable...');
            waitForJTable();
        });
        
        // Función de logout
        function logout() {
            if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
                $.ajax({
                    url: '/api/logout',
                    method: 'POST',
                    success: function() {
                        localStorage.removeItem('auth_token');
                        localStorage.removeItem('user_data');
                        alert('Sesión cerrada exitosamente');
                        window.location.href = '/';
                    },
                    error: function() {
                        localStorage.removeItem('auth_token');
                        localStorage.removeItem('user_data');
                        window.location.href = '/';
                    }
                });
            }
        }
    </script>
</body>
</html>