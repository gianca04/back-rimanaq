<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lecciones del Curso - Rimanaq</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables CSS (alternativa más estable a jTable) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <!-- jQuery UI CSS (required by jTable) -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/ui-lightness/jquery-ui.css">
    
    <!-- jTable CSS - usando archivos locales como fallback -->
    <link rel="stylesheet" href="/css/jtable/jtable-basic.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/hikalkan/jtable@latest/lib/themes/lightcolor/blue/jtable.css" onerror="console.log('jTable CSS no cargó, usando fallback local')">
    
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
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/dashboard">Rimanaq Admin</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard/courses">Cursos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/dashboard/lessons">Lecciones</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard/gestures">Gestos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard/progress">Progreso</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <button class="btn btn-outline-light" onclick="logout()">Cerrar Sesión</button>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables Script (fallback más estable) -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- jTable Script - usando URL más confiable -->
    <script>
        // Cargar jTable directamente
        (function() {
            var script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/jtable@2.5.0/lib/jquery.jtable.min.js';
            script.onload = function() {
                console.log('jTable cargado exitosamente desde jsDelivr');
            };
            script.onerror = function() {
                console.log('Fallback: cargando jTable desde GitHub');
                var fallbackScript = document.createElement('script');
                fallbackScript.src = 'https://raw.githubusercontent.com/hikalkan/jtable/master/lib/jquery.jtable.min.js';
                document.head.appendChild(fallbackScript);
            };
            document.head.appendChild(script);
        })();
    </script>

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

        // Verificación simplificada de jTable
        function waitForJTable() {
            if (typeof $.fn.jtable !== 'undefined') {
                console.log('jTable disponible, inicializando...');
                initializeApp();
            } else {
                console.log('Esperando jTable...');
                setTimeout(waitForJTable, 500);
            }
        }

        function initializeApp() {
            // Verificar autenticación
            const token = localStorage.getItem('auth_token');
            if (!token) {
                window.location.href = '/';
                return;
            }

            // Obtener ID del curso desde la URL
            const pathParts = window.location.pathname.split('/');
            console.log('Path parts:', pathParts);
            console.log('Full pathname:', window.location.pathname);
            
            courseId = pathParts[3]; // /dashboard/courses/{id}/lessons
            
            // Validar que courseId es un número válido
            if (!courseId || isNaN(courseId)) {
                console.error('Invalid courseId from URL:', courseId);
                alert('Error: ID del curso no válido en la URL');
                window.location.href = '/dashboard/courses';
                return;
            }
            
            courseId = parseInt(courseId);
            console.log('Parsed courseId:', courseId);

            // Obtener nombre del curso desde los parámetros de la URL
            const urlParams = new URLSearchParams(window.location.search);
            courseName = urlParams.get('courseName') || 'Curso';

            // Actualizar título
            document.getElementById('course-title').textContent = 'Lecciones de: ' + courseName;

            console.log('Inicializando aplicación con jTable para curso ID:', courseId);
            initializeLessonsTable();
        }

        function initializeLessonsTable() {
            console.log('=== INITIALIZING JTABLE ===');
            console.log('courseId at table init:', courseId);
            console.log('courseName at table init:', courseName);
            
            // Inicializar jTable para Lecciones del curso específico
            $('#LessonsTableContainer').jtable({
                title: 'Lecciones del Curso: ' + courseName,
                paging: true,
                pageSize: 10,
                sorting: true,
                defaultSorting: 'level_number ASC',
                
                actions: {
                    listAction: function (postData) {
                        return $.ajax({
                            url: '/api/courses/' + courseId + '/lessons',
                            type: 'GET',
                            dataType: 'json',
                        }).then(function(response) {
                            return {
                                Result: 'OK',
                                Records: response.data || [],
                                TotalRecordCount: (response.data || []).length
                            };
                        }).catch(function(xhr) {
                            return {
                                Result: 'ERROR',
                                Message: 'Error al cargar lecciones: ' + (xhr.responseJSON?.message || xhr.statusText)
                            };
                        });
                    },
                    
                    createAction: function (postData) {
                        console.log('=== CREATE ACTION DEBUG ===');
                        console.log('courseId variable:', courseId);
                        console.log('postData type:', typeof postData);
                        console.log('postData raw:', postData);
                        
                        // Si postData es un string, lo parseamos a objeto
                        let formData = {};
                        if (typeof postData === 'string') {
                            console.log('postData is string, parsing...');
                            // Parsear la cadena URL-encoded a objeto
                            const params = new URLSearchParams(postData);
                            for (let [key, value] of params.entries()) {
                                formData[key] = decodeURIComponent(value);
                            }
                            console.log('parsed formData:', formData);
                        } else {
                            console.log('postData is object');
                            formData = { ...postData };
                        }
                        
                        // Asegurar que course_id esté presente y sea el correcto
                        formData.course_id = courseId;
                        
                        // Validar que courseId no sea null/undefined
                        if (!courseId || courseId === 'undefined' || courseId === null || isNaN(courseId)) {
                            console.error('courseId is invalid:', courseId);
                            alert('Error: ID del curso no válido. CourseId: ' + courseId);
                            return Promise.resolve({
                                Result: 'ERROR',
                                Message: 'Error: ID del curso no válido'
                            });
                        }
                        
                        // Asegurar que course_id sea el correcto (como entero)
                        formData.course_id = parseInt(courseId);
                        
                        // Validar otros campos requeridos
                        if (!formData.name || formData.name.trim() === '') {
                            alert('El nombre es requerido');
                            return Promise.resolve({
                                Result: 'ERROR',
                                Message: 'El nombre es requerido'
                            });
                        }
                        
                        if (!formData.description || formData.description.trim() === '') {
                            alert('La descripción es requerida');
                            return Promise.resolve({
                                Result: 'ERROR',
                                Message: 'La descripción es requerida'
                            });
                        }
                        
                        // Convertir números
                        formData.level_number = parseInt(formData.level_number) || 1;
                        formData.time_minutes = parseInt(formData.time_minutes) || 30;
                        
                        console.log('formData after processing:', JSON.stringify(formData, null, 2));
                        console.log('=== SENDING REQUEST ===');
                        
                        return $.ajax({
                            url: '/api/lessons',
                            type: 'POST',
                            dataType: 'json',
                            data: formData,
                            contentType: 'application/x-www-form-urlencoded'
                        }).then(function(response) {
                            return {
                                Result: 'OK',
                                Record: response.data
                            };
                        }).catch(function(xhr) {
                            console.error('=== AJAX ERROR ===');
                            console.error('Status:', xhr.status);
                            console.error('Response Text:', xhr.responseText);
                            console.error('Response JSON:', xhr.responseJSON);
                            console.error('Full XHR:', xhr);
                            
                            let errorMessage = 'Error al crear lección';
                            
                            if (xhr.responseJSON) {
                                if (xhr.responseJSON.errors) {
                                    // Mostrar errores de validación específicos
                                    console.error('Validation errors:', xhr.responseJSON.errors);
                                    const errorsList = Object.entries(xhr.responseJSON.errors).map(([field, messages]) => {
                                        return `${field}: ${Array.isArray(messages) ? messages.join(', ') : messages}`;
                                    });
                                    errorMessage = 'Errores de validación:\n' + errorsList.join('\n');
                                    alert(errorMessage); // Mostrar alert detallado
                                } else if (xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                    alert(errorMessage);
                                }
                            } else {
                                errorMessage = `Error ${xhr.status}: ${xhr.statusText}`;
                                alert(errorMessage);
                            }
                            
                            return {
                                Result: 'ERROR',
                                Message: errorMessage
                            };
                        });
                    },
                    
                    updateAction: function (postData) {
                        postData.course_id = courseId;
                        return $.ajax({
                            url: '/api/lessons/' + postData.id,
                            type: 'PUT',
                            dataType: 'json',
                            data: postData,
                            contentType: 'application/x-www-form-urlencoded'
                        }).then(function(response) {
                            return {
                                Result: 'OK',
                                Record: response.data
                            };
                        }).catch(function(xhr) {
                            return {
                                Result: 'ERROR',
                                Message: 'Error al actualizar lección: ' + (xhr.responseJSON?.message || xhr.statusText)
                            };
                        });
                    },
                    
                    deleteAction: function (postData) {
                        return $.ajax({
                            url: '/api/lessons/' + postData.id,
                            type: 'DELETE',
                            dataType: 'json'
                        }).then(function(response) {
                            return {
                                Result: 'OK'
                            };
                        }).catch(function(xhr) {
                            return {
                                Result: 'ERROR',
                                Message: 'Error al eliminar lección: ' + (xhr.responseJSON?.message || xhr.statusText)
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
                            console.log('Getting defaultValue for course_id:', courseId);
                            return courseId;
                        },
                        create: true,
                        edit: false, // No permitir editar en updates
                        list: false
                    },
                    name: {
                        title: 'Nombre',
                        width: '25%',
                        inputClass: 'form-control'
                    },
                    description: {
                        title: 'Descripción',
                        width: '35%',
                        type: 'textarea',
                        inputClass: 'form-control'
                    },
                    level_number: {
                        title: 'Nivel',
                        width: '10%',
                        inputClass: 'form-control',
                        type: 'number',
                        defaultValue: 1
                    },
                    difficulty: {
                        title: 'Dificultad',
                        width: '10%',
                        type: 'option',
                        inputClass: 'form-select',
                        options: {
                            'fácil': 'Fácil',
                            'intermedio': 'Intermedio',
                            'difícil': 'Difícil'
                        },
                        defaultValue: 'fácil'
                    },
                    time_minutes: {
                        title: 'Tiempo (min)',
                        width: '10%',
                        inputClass: 'form-control',
                        type: 'number',
                        defaultValue: 30
                    },
                    Actions: {
                        title: 'Gestos',
                        width: '15%',
                        sorting: false,
                        edit: false,
                        create: false,
                        display: function (data) {
                            return '<button type="button" class="btn btn-sm btn-success" onclick="viewLessonGestures(' + data.record.id + ', \'' + data.record.name.replace(/'/g, "\\'") + '\')">Ver Gestos</button>';
                        }
                    }
                }
            });

            // Cargar datos
            $('#LessonsTableContainer').jtable('load');
            
            // Event handler para cuando se crea el formulario
            $('#LessonsTableContainer').on('formCreated', function (event, data) {
                console.log('=== FORM CREATED EVENT ===');
                console.log('Form type:', data.formType);
                console.log('Current courseId:', courseId);
                
                if (data.formType === 'create') {
                    // Asegurar que el course_id esté presente y sea correcto
                    setTimeout(function() {
                        const courseIdInput = data.form.find('input[name="course_id"]');
                        console.log('Found course_id input:', courseIdInput.length > 0);
                        
                        if (courseIdInput.length > 0) {
                            courseIdInput.val(courseId);
                            console.log('Set course_id in hidden field:', courseId);
                            console.log('Verified value in field:', courseIdInput.val());
                        } else {
                            console.warn('Hidden course_id field not found - adding manually');
                            // Si no existe, agregarlo manualmente
                            data.form.append('<input type="hidden" name="course_id" value="' + courseId + '">');
                        }
                        
                        // Debug: listar todos los campos del formulario
                        console.log('All form fields:');
                        data.form.find('input, select, textarea').each(function(i, field) {
                            console.log(`  ${field.name}: "${field.value}" (type: ${field.type})`);
                        });
                    }, 50);
                }
                console.log('=== END FORM CREATED ===');
            });
        }

        // Función para ver gestos de una lección
        function viewLessonGestures(lessonId, lessonName) {
            window.location.href = '/dashboard/lessons/' + lessonId + '/gestures?lessonName=' + encodeURIComponent(lessonName) + '&courseId=' + courseId + '&courseName=' + encodeURIComponent(courseName);
        }

        // Inicializar cuando esté listo
        $(document).ready(function() {
            console.log('DOM listo, esperando jTable...');
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