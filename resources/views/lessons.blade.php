<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Lecciones - Rimanaq</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- jTable CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jtable/2.5.0/themes/lightcolor/blue/jtable.min.css">
    
    <!-- jQuery UI CSS (required by jTable) -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">
    
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
        
        /* Estilos para los campos del formulario */
        .jtable-input-field-container input,
        .jtable-input-field-container select,
        .jtable-input-field-container textarea {
            width: 100% !important;
        }
        
        .jtable-input-field-container select.form-select {
            padding: 0.375rem 0.75rem;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
        }
        
        /* Mejorar apariencia de los botones en la tabla */
        .btn-sm {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }
        
        /* Estilos para mejor presentación del formulario */
        .jtable-input select[name="course_id"] {
            background-color: #f8f9fa;
            border: 2px solid #dee2e6;
        }
        
        .jtable-input select[name="course_id"]:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
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
                <h1>Gestión de Lecciones</h1>
                <p class="text-muted">Administra las lecciones de cada curso.</p>
                
                <div id="LessonsTableContainer" style="margin-top: 20px;"></div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jtable/2.5.0/jquery.jtable.min.js"></script>

    <script>
        let coursesOptions = {};

        // Configurar CSRF token para todas las peticiones
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token')
            }
        });

        $(document).ready(function() {
            // Verificar autenticación
            const token = localStorage.getItem('auth_token');
            if (!token) {
                window.location.href = '/';
                return;
            }

            // Obtener el ID del curso de la URL si estamos viendo lecciones de un curso específico
            const urlParams = new URLSearchParams(window.location.search);
            const currentCourseId = getCourseIdFromUrl();
            
            console.log('ID del curso detectado en la URL:', currentCourseId);

            // Cargar cursos para el dropdown
            loadCourses().then(function() {
                initializeLessonsTable(currentCourseId);
            });
        });

        // Función para extraer el ID del curso de la URL
        function getCourseIdFromUrl() {
            // Buscar patrones como /courses/ID/lessons en la URL
            const path = window.location.pathname;
            const match = path.match(/\/courses\/(\d+)\/lessons/);
            if (match) {
                return parseInt(match[1]);
            }
            
            // También verificar parámetros de consulta
            const urlParams = new URLSearchParams(window.location.search);
            const courseId = urlParams.get('courseId');
            if (courseId) {
                return parseInt(courseId);
            }
            
            return null;
        }

        function loadCourses() {
            return $.ajax({
                url: '/api/courses',
                type: 'GET',
                dataType: 'json'
            }).then(function(response) {
                coursesOptions = {};
                console.log('Cursos recibidos:', response);
                
                if (response.data && response.data.length > 0) {
                    response.data.forEach(function(course) {
                        coursesOptions[course.id] = course.name;
                    });
                } else if (response.success === false) {
                    console.warn('No se pudieron cargar los cursos');
                    alert('Advertencia: No se pudieron cargar los cursos. Asegúrate de crear al menos un curso primero.');
                }
                
                console.log('Opciones de cursos configuradas:', coursesOptions);
            }).catch(function(xhr) {
                console.error('Error cargando cursos:', xhr);
                alert('Error al cargar la lista de cursos: ' + (xhr.responseJSON?.message || xhr.statusText));
            });
        }

        function initializeLessonsTable(preselectedCourseId = null) {
            console.log('Inicializando aplicación con jTable para curso ID:', preselectedCourseId);
            
            // Inicializar jTable para Lecciones
            $('#LessonsTableContainer').jtable({
                title: 'Lecciones',
                paging: true,
                pageSize: 10,
                sorting: true,
                defaultSorting: 'id ASC',
                
                actions: {
                    listAction: function (postData) {
                        return $.ajax({
                            url: '/api/lessons',
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
                        console.log('=== CREAR LECCIÓN - DEBUG ===');
                        console.log('Datos originales recibidos:', postData);
                        
                        // Si no hay course_id o está vacío, usar el preseleccionado
                        if (!postData.course_id || postData.course_id === '' || postData.course_id === '0') {
                            if (preselectedCourseId) {
                                console.log('Usando curso preseleccionado:', preselectedCourseId);
                                postData.course_id = preselectedCourseId;
                            } else if (Object.keys(coursesOptions).length > 0) {
                                const firstCourseId = Object.keys(coursesOptions)[0];
                                console.log('Usando primer curso disponible:', firstCourseId);
                                postData.course_id = firstCourseId;
                            }
                        }
                        
                        // Convertir a números los campos que lo requieren
                        postData.course_id = parseInt(postData.course_id) || 0;
                        postData.level_number = parseInt(postData.level_number) || 1;
                        postData.time_minutes = parseInt(postData.time_minutes) || 30;
                        
                        console.log('Datos procesados a enviar:', postData);
                        
                        // Validación final
                        if (!postData.course_id || postData.course_id === 0) {
                            console.error('ERROR: course_id inválido');
                            return Promise.resolve({
                                Result: 'ERROR',
                                Message: 'Error: Debe seleccionar un curso válido.'
                            });
                        }
                        
                        if (!postData.name || postData.name.trim() === '') {
                            return Promise.resolve({
                                Result: 'ERROR',
                                Message: 'Error: El nombre de la lección es obligatorio.'
                            });
                        }
                        
                        if (!postData.description || postData.description.trim() === '') {
                            return Promise.resolve({
                                Result: 'ERROR',
                                Message: 'Error: La descripción de la lección es obligatoria.'
                            });
                        }
                        
                        if (!postData.difficulty || postData.difficulty.trim() === '') {
                            return Promise.resolve({
                                Result: 'ERROR',
                                Message: 'Error: La dificultad es obligatoria.'
                            });
                        }
                        
                        console.log('=== ENVIANDO PETICIÓN ===');
                        
                        return $.ajax({
                            url: '/api/lessons',
                            type: 'POST',
                            dataType: 'json',
                            data: postData,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                                'Content-Type': 'application/x-www-form-urlencoded'
                            }
                        }).then(function(response) {
                            console.log('✅ RESPUESTA EXITOSA:', response);
                            return {
                                Result: 'OK',
                                Record: response.data
                            };
                        }).catch(function(xhr) {
                            console.error('❌ ERROR EN LA PETICIÓN:', xhr);
                            console.error('Status:', xhr.status);
                            console.error('Response:', xhr.responseJSON);
                            
                            let errorMessage = 'Error al crear lección: ';
                            
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                const errors = xhr.responseJSON.errors;
                                const errorMessages = [];
                                Object.keys(errors).forEach(function(field) {
                                    errorMessages.push(field + ': ' + errors[field].join(', '));
                                });
                                errorMessage += errorMessages.join(' | ');
                            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage += xhr.responseJSON.message;
                            } else {
                                errorMessage += xhr.statusText || 'Error desconocido';
                            }
                            
                            return {
                                Result: 'ERROR',
                                Message: errorMessage
                            };
                        });
                    },
                    
                    updateAction: function (postData) {
                        console.log('Datos de actualización:', postData);
                        
                        // Asegurar que course_id sea un número
                        if (postData.course_id) {
                            postData.course_id = parseInt(postData.course_id);
                        }
                        
                        // Asegurar que level_number sea un número
                        if (postData.level_number) {
                            postData.level_number = parseInt(postData.level_number);
                        }
                        
                        // Asegurar que time_minutes sea un número
                        if (postData.time_minutes) {
                            postData.time_minutes = parseInt(postData.time_minutes);
                        }
                        
                        return $.ajax({
                            url: '/api/lessons/' + postData.id,
                            type: 'PUT',
                            dataType: 'json',
                            data: postData,
                            contentType: 'application/x-www-form-urlencoded'
                        }).then(function(response) {
                            console.log('Actualización exitosa:', response);
                            return {
                                Result: 'OK',
                                Record: response.data
                            };
                        }).catch(function(xhr) {
                            console.error('Error en actualización:', xhr);
                            let errorMessage = 'Error al actualizar lección: ';
                            
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                const errors = xhr.responseJSON.errors;
                                const errorMessages = [];
                                Object.keys(errors).forEach(function(field) {
                                    errorMessages.push(errors[field][0]);
                                });
                                errorMessage += errorMessages.join(', ');
                            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage += xhr.responseJSON.message;
                            } else {
                                errorMessage += xhr.statusText;
                            }
                            
                            return {
                                Result: 'ERROR',
                                Message: errorMessage
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
                        width: '4%'
                    },
                    name: {
                        title: 'Nombre',
                        width: '18%',
                        inputClass: 'form-control'
                    },
                    description: {
                        title: 'Descripción',
                        width: '25%',
                        type: 'textarea',
                        inputClass: 'form-control'
                    },
                    course_id: {
                        title: 'Curso',
                        width: '15%',
                        type: 'option',
                        options: coursesOptions,
                        defaultValue: preselectedCourseId || Object.keys(coursesOptions)[0] || '',
                        display: function(data) {
                            return coursesOptions[data.record.course_id] || 'N/A';
                        }
                    },
                    level_number: {
                        title: 'Nivel',
                        width: '8%',
                        inputClass: 'form-control',
                        type: 'number',
                        defaultValue: 1
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
                        title: 'Duración (min)',
                        width: '10%',
                        type: 'number',
                        inputClass: 'form-control',
                        defaultValue: 30
                    },
                    Actions: {
                        title: 'Gestos',
                        width: '8%',
                        sorting: false,
                        edit: false,
                        create: false,
                        display: function (data) {
                            return '<button type="button" class="btn btn-sm btn-info" onclick="viewLessonGestures(' + data.record.id + ', \'' + data.record.name.replace(/'/g, "\\'") + '\')">Ver Gestos</button>';
                        }
                    }
                }
            });

            // Cargar datos
            $('#LessonsTableContainer').jtable('load');
            
            // Manejar eventos de jTable
            $('#LessonsTableContainer').on('formCreated', function (event, data) {
                console.log('Formulario creado para:', data.formType);
                
                // Si estamos creando y hay un curso preseleccionado, asegurar que esté seleccionado
                if (data.formType === 'create' && preselectedCourseId) {
                    setTimeout(function() {
                        $('select[name="course_id"]').val(preselectedCourseId);
                        console.log('Curso preseleccionado establecido:', preselectedCourseId);
                    }, 50);
                }
            });
        }



        // Función para ver gestos de una lección
        function viewLessonGestures(lessonId, lessonName) {
            window.location.href = '/dashboard/lessons/' + lessonId + '/gestures?lessonName=' + encodeURIComponent(lessonName);
        }

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