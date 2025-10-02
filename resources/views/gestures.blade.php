<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Gestos - Rimanaq</title>
    
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
                <h1>Gestión de Gestos</h1>
                <p class="text-muted">Administra los gestos de cada lección.</p>
                
                <div id="GesturesTableContainer" style="margin-top: 20px;"></div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jtable/2.5.0/jquery.jtable.min.js"></script>

    <script>
        let lessonsOptions = {};

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

            // Cargar lecciones para el dropdown
            loadLessons().then(function() {
                initializeGesturesTable();
            });
        });

        function loadLessons() {
            return $.ajax({
                url: '/api/lessons',
                type: 'GET',
                dataType: 'json'
            }).then(function(response) {
                lessonsOptions = {};
                if (response.data && response.data.length > 0) {
                    response.data.forEach(function(lesson) {
                        lessonsOptions[lesson.id] = lesson.name + (lesson.course ? ' (' + lesson.course.name + ')' : '');
                    });
                }
            }).catch(function(xhr) {
                console.error('Error cargando lecciones:', xhr);
            });
        }

        function initializeGesturesTable() {
            // Inicializar jTable para Gestos
            $('#GesturesTableContainer').jtable({
                title: 'Gestos',
                paging: true,
                pageSize: 10,
                sorting: true,
                defaultSorting: 'id ASC',
                
                actions: {
                    listAction: function (postData) {
                        return $.ajax({
                            url: '/api/gestures',
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
                    lesson_id: {
                        title: 'Lección',
                        width: '35%',
                        options: lessonsOptions,
                        display: function(data) {
                            return lessonsOptions[data.record.lesson_id] || 'N/A';
                        }
                    },
                    lesson: {
                        title: 'Lección Info',
                        create: false,
                        edit: false,
                        list: false,
                        display: function(data) {
                            if (data.record.lesson) {
                                return data.record.lesson.name;
                            }
                            return 'N/A';
                        }
                    }
                }
            });

            // Cargar datos
            $('#GesturesTableContainer').jtable('load');
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