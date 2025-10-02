<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestos de la Lección - Rimanaq</title>
    
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
        .lesson-info {
            background-color: #e8f5e8;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #4caf50;
        }
        .course-info {
            background-color: #fff3e0;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            border-left: 3px solid #ff9800;
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
                        <a class="nav-link" href="/dashboard/lessons">Lecciones</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/dashboard/gestures">Gestos</a>
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
                        <li class="breadcrumb-item"><a href="#" id="course-breadcrumb">Curso</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Gestos</li>
                    </ol>
                </nav>

                <!-- Información del curso -->
                <div class="course-info">
                    <small><strong>Curso:</strong> <span id="course-name">Cargando...</span></small>
                </div>

                <!-- Información de la lección -->
                <div class="lesson-info">
                    <h4 id="lesson-title">Cargando información de la lección...</h4>
                    <p class="mb-0">Gestiona los gestos de esta lección</p>
                </div>
                
                <div id="GesturesTableContainer" style="margin-top: 20px;"></div>
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
        let lessonId = null;
        let lessonName = '';
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

            // Obtener parámetros desde la URL
            const pathParts = window.location.pathname.split('/');
            lessonId = pathParts[3]; // /dashboard/lessons/{id}/gestures

            const urlParams = new URLSearchParams(window.location.search);
            lessonName = urlParams.get('lessonName') || 'Lección';
            courseId = urlParams.get('courseId') || '';
            courseName = urlParams.get('courseName') || 'Curso';

            // Actualizar elementos de la interfaz
            document.getElementById('lesson-title').textContent = 'Gestos de: ' + lessonName;
            document.getElementById('course-name').textContent = courseName;
            
            // Configurar breadcrumb del curso
            const courseBreadcrumb = document.getElementById('course-breadcrumb');
            if (courseId) {
                courseBreadcrumb.href = '/dashboard/courses/' + courseId + '/lessons?courseName=' + encodeURIComponent(courseName);
                courseBreadcrumb.textContent = courseName;
            }

            console.log('Inicializando aplicación con jTable para lección ID:', lessonId);
            initializeGesturesTable();
        }

        function initializeGesturesTable() {
            // Inicializar jTable para Gestos de la lección específica
            $('#GesturesTableContainer').jtable({
                title: 'Gestos de la Lección: ' + lessonName,
                paging: true,
                pageSize: 10,
                sorting: true,
                defaultSorting: 'id ASC',
                
                actions: {
                    listAction: function (postData) {
                        return $.ajax({
                            url: '/api/lessons/' + lessonId + '/gestures',
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
                                Message: 'Error al cargar gestos: ' + (xhr.responseJSON?.message || xhr.statusText)
                            };
                        });
                    },
                    
                    createAction: function (postData) {
                        // Agregar el lesson_id automáticamente
                        postData.lesson_id = lessonId;
                        return $.ajax({
                            url: '/api/gestures',
                            type: 'POST',
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
                                Message: 'Error al crear gesto: ' + (xhr.responseJSON?.message || xhr.statusText)
                            };
                        });
                    },
                    
                    updateAction: function (postData) {
                        postData.lesson_id = lessonId;
                        return $.ajax({
                            url: '/api/gestures/' + postData.id,
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
                                Message: 'Error al actualizar gesto: ' + (xhr.responseJSON?.message || xhr.statusText)
                            };
                        });
                    },
                    
                    deleteAction: function (postData) {
                        return $.ajax({
                            url: '/api/gestures/' + postData.id,
                            type: 'DELETE',
                            dataType: 'json'
                        }).then(function(response) {
                            return {
                                Result: 'OK'
                            };
                        }).catch(function(xhr) {
                            return {
                                Result: 'ERROR',
                                Message: 'Error al eliminar gesto: ' + (xhr.responseJSON?.message || xhr.statusText)
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
                    name: {
                        title: 'Nombre del Gesto',
                        width: '30%',
                        inputClass: 'form-control'
                    },
                    description: {
                        title: 'Descripción',
                        width: '50%',
                        type: 'textarea',
                        inputClass: 'form-control'
                    },
                    gesture_data: {
                        title: 'Datos del Gesto',
                        width: '15%',
                        type: 'textarea',
                        inputClass: 'form-control',
                        display: function (data) {
                            if (data.record.gesture_data) {
                                try {
                                    const gestureData = typeof data.record.gesture_data === 'string' 
                                        ? JSON.parse(data.record.gesture_data) 
                                        : data.record.gesture_data;
                                    return '<small>JSON: ' + Object.keys(gestureData).length + ' campos</small>';
                                } catch (e) {
                                    return '<small>Datos JSON</small>';
                                }
                            }
                            return '<small>Sin datos</small>';
                        },
                        input: function (data) {
                            if (data.record && data.record.gesture_data) {
                                const jsonString = typeof data.record.gesture_data === 'string' 
                                    ? data.record.gesture_data 
                                    : JSON.stringify(data.record.gesture_data, null, 2);
                                return '<textarea class="form-control" style="height: 100px;" placeholder="Ingrese datos JSON del gesto">' + jsonString + '</textarea>';
                            }
                            return '<textarea class="form-control" style="height: 100px;" placeholder="Ingrese datos JSON del gesto"></textarea>';
                        }
                    }
                }
            });

            // Cargar datos
            $('#GesturesTableContainer').jtable('load');
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