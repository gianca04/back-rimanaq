<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Cursos - Rimanaq</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables CSS (alternativa más estable a jTable) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <!-- jQuery UI CSS (required by jTable) -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/ui-lightness/jquery-ui.css">
    
    <!-- jTable CSS - usando archivos locales como fallback -->
    <link rel="stylesheet" href="/css/jtable/jtable-basic.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/hikalkan/jtable@latest/lib/themes/lightcolor/blue/jtable.css" onerror="console.log('jTable CSS no cargó, usando fallback local')">
    
    <!-- Spectrum Color Picker CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/spectrum-colorpicker2@2.0.9/dist/spectrum.min.css">
    
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
        
        /* Estilos para el selector de color mejorado */
        .color-preview {
            display: inline-block;
            width: 30px;
            height: 30px;
            border: 2px solid #ddd;
            border-radius: 50%;
            margin-right: 10px;
            vertical-align: middle;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .color-options {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }
        
        .color-option {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .color-option:hover {
            transform: scale(1.1);
            border-color: #007bff;
        }
        
        .color-option.selected {
            border-color: #007bff;
            box-shadow: 0 0 0 2px #007bff40;
        }
        
        .custom-color-input {
            margin-top: 10px;
        }
        
        .sp-container {
            z-index: 9999 !important;
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
                        <a class="nav-link active" href="/dashboard/courses">Cursos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard/lessons">Lecciones</a>
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
                <h1>Gestión de Cursos</h1>
                <p class="text-muted">Administra los cursos de la plataforma Rimanaq.</p>
                
                <div id="CoursesTableContainer" style="margin-top: 20px;"></div>
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
    
    <!-- Spectrum Color Picker Script -->
    <script src="https://cdn.jsdelivr.net/npm/spectrum-colorpicker2@2.0.9/dist/spectrum.min.js"></script>
    
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
    </script>

    <script>
        // Configurar CSRF token para todas las peticiones
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token')
            }
        });

        function initializeApp() {
            // Verificar autenticación
            const token = localStorage.getItem('auth_token');
            if (!token) {
                window.location.href = '/';
                return;
            }

            console.log('Inicializando aplicación con jTable...');
            initializeCoursesTable();
        }

        // Función alternativa usando DataTables
        function initializeDataTable() {
            // Verificar autenticación
            const token = localStorage.getItem('auth_token');
            if (!token) {
                window.location.href = '/';
                return;
            }

            console.log('Inicializando con DataTables...');
            
            // Crear tabla HTML básica
            $('#CoursesTableContainer').html(`
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Cursos</h4>
                    <button class="btn btn-primary" onclick="showAddCourseModal()">Agregar Curso</button>
                </div>
                <table id="coursesDataTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Imagen</th>
                            <th>Color</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            `);

            // Cargar datos y inicializar DataTable
            loadCoursesData();
        }

        // Cargar datos de cursos para DataTable
        function loadCoursesData() {
            $.ajax({
                url: '/api/courses',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    const courses = response.data || [];
                    let tableData = [];
                    
                    courses.forEach(function(course) {
                        tableData.push([
                            course.id,
                            course.name || '',
                            course.description || '',
                            course.image_path || '',
                            course.color ? `<div style="width: 25px; height: 25px; background-color: ${course.color}; border: 2px solid #ddd; border-radius: 50%; display: inline-block; margin-right: 8px; vertical-align: middle;"></div><span style="vertical-align: middle;">${course.color}</span>` : '<span class="text-muted">Sin color</span>',
                            `<button class="btn btn-sm btn-warning me-1" onclick="editCourse(${course.id})">Editar</button>
                             <button class="btn btn-sm btn-danger" onclick="deleteCourse(${course.id})">Eliminar</button>`
                        ]);
                    });

                    $('#coursesDataTable').DataTable({
                        data: tableData,
                        responsive: true,
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                        }
                    });
                },
                error: function(xhr) {
                    console.error('Error cargando cursos:', xhr);
                    alert('Error al cargar los cursos: ' + (xhr.responseJSON?.message || xhr.statusText));
                }
            });
        }

        function initializeCoursesTable() {
            $('#CoursesTableContainer').jtable({
                title: 'Cursos',
                paging: true,
                pageSize: 10,
                sorting: true,
                defaultSorting: 'id ASC',
                
                actions: {
                    listAction: function (postData) {
                        return $.ajax({
                            url: '/api/courses',
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
                                Message: 'Error al cargar cursos: ' + (xhr.responseJSON?.message || xhr.statusText)
                            };
                        });
                    },
                    
                    createAction: function (postData) {
                        return $.ajax({
                            url: '/api/courses',
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
                                Message: 'Error al crear curso: ' + (xhr.responseJSON?.message || xhr.statusText)
                            };
                        });
                    },
                    
                    updateAction: function (postData) {
                        return $.ajax({
                            url: '/api/courses/' + postData.id,
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
                                Message: 'Error al actualizar curso: ' + (xhr.responseJSON?.message || xhr.statusText)
                            };
                        });
                    },
                    
                    deleteAction: function (postData) {
                        return $.ajax({
                            url: '/api/courses/' + postData.id,
                            type: 'DELETE',
                            dataType: 'json'
                        }).then(function(response) {
                            return {
                                Result: 'OK'
                            };
                        }).catch(function(xhr) {
                            return {
                                Result: 'ERROR',
                                Message: 'Error al eliminar curso: ' + (xhr.responseJSON?.message || xhr.statusText)
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
                        width: '40%',
                        type: 'textarea',
                        inputClass: 'form-control'
                    },
                    image_path: {
                        title: 'Ruta de Imagen',
                        width: '20%',
                        inputClass: 'form-control'
                    },
                    color: {
                        title: 'Color',
                        width: '8%',
                        input: function (data) {
                            // Colores predefinidos para cursos educativos
                            const predefinedColors = [
                                '#3B82F6', // Azul
                                '#10B981', // Verde esmeralda
                                '#EF4444', // Rojo
                                '#F59E0B', // Amarillo
                                '#8B5CF6', // Púrpura
                                '#06B6D4', // Cian
                                '#EC4899', // Rosa
                                '#84CC16', // Verde lima
                                '#F97316', // Naranja
                                '#6366F1'  // Índigo
                            ];
                            
                            const currentColor = data.record ? data.record.color || '#3B82F6' : '#3B82F6';
                            
                            let html = '<div class="color-selector-container">';
                            
                            // Vista previa del color actual
                            html += '<div class="mb-2">';
                            html += '<label class="form-label">Color del curso:</label>';
                            html += '<div class="d-flex align-items-center">';
                            html += '<div class="color-preview" id="color-preview" style="background-color: ' + currentColor + '"></div>';
                            html += '<span id="color-text">' + currentColor + '</span>';
                            html += '</div>';
                            html += '</div>';
                            
                            // Colores predefinidos
                            html += '<div class="mb-2">';
                            html += '<label class="form-label small">Colores sugeridos:</label>';
                            html += '<div class="color-options">';
                            predefinedColors.forEach(function(color) {
                                const selectedClass = color === currentColor ? ' selected' : '';
                                html += '<div class="color-option' + selectedClass + '" data-color="' + color + '" style="background-color: ' + color + '"></div>';
                            });
                            html += '</div>';
                            html += '</div>';
                            
                            // Input oculto que contendrá el valor real
                            html += '<input type="hidden" class="color-input-value" name="color" value="' + currentColor + '" />';
                            
                            // Selector de color personalizado
                            html += '<div class="custom-color-input">';
                            html += '<label class="form-label small">Color personalizado:</label>';
                            html += '<input type="color" class="form-control form-control-color custom-color-picker" value="' + currentColor + '" style="width: 50px; height: 35px;" />';
                            html += '</div>';
                            
                            html += '</div>';
                            
                            // Agregar el script para manejar la interactividad
                            setTimeout(function() {
                                initializeColorSelector();
                            }, 100);
                            
                            return html;
                        },
                        display: function (data) {
                            if (data.record.color) {
                                return '<div style="width: 25px; height: 25px; background-color: ' + data.record.color + '; border: 2px solid #ddd; border-radius: 50%; display: inline-block; margin-right: 8px; vertical-align: middle;"></div><span style="vertical-align: middle;">' + data.record.color + '</span>';
                            }
                            return '<span class="text-muted">Sin color</span>';
                        }
                    },
                    Actions: {
                        title: 'Lecciones',
                        width: '12%',
                        sorting: false,
                        edit: false,
                        create: false,
                        display: function (data) {
                            return '<button type="button" class="btn btn-sm btn-info" onclick="viewCourseLessons(' + data.record.id + ', \'' + data.record.name.replace(/'/g, "\\'") + '\')">Ver Lecciones</button>';
                        }
                    }
                }
            });

            // Cargar datos
            $('#CoursesTableContainer').jtable('load');
            
            // Manejar eventos de jTable para inicializar selectores de color
            $('#CoursesTableContainer').on('formCreated', function (event, data) {
                initializeColorSelector();
            });
            
            $('#CoursesTableContainer').on('formClosed', function (event, data) {
                // Limpiar eventos cuando se cierre el modal
                $(document).off('click', '.color-option');
                $(document).off('change', '.custom-color-picker');
                $(document).off('click', '#color-preview');
            });
        }

        // Función para inicializar el selector de color
        function initializeColorSelector() {
            // Manejar clics en colores predefinidos
            $(document).off('click', '.color-option').on('click', '.color-option', function() {
                const selectedColor = $(this).data('color');
                
                // Actualizar vista previa
                $('#color-preview').css('background-color', selectedColor);
                $('#color-text').text(selectedColor);
                
                // Actualizar input oculto
                $('.color-input-value').val(selectedColor);
                
                // Actualizar selector personalizado
                $('.custom-color-picker').val(selectedColor);
                
                // Actualizar clases de selección
                $('.color-option').removeClass('selected');
                $(this).addClass('selected');
            });
            
            // Manejar cambios en el selector personalizado
            $(document).off('change', '.custom-color-picker').on('change', '.custom-color-picker', function() {
                const selectedColor = $(this).val();
                
                // Actualizar vista previa
                $('#color-preview').css('background-color', selectedColor);
                $('#color-text').text(selectedColor);
                
                // Actualizar input oculto
                $('.color-input-value').val(selectedColor);
                
                // Quitar selección de colores predefinidos si no coincide
                $('.color-option').removeClass('selected');
                $('.color-option[data-color="' + selectedColor + '"]').addClass('selected');
            });
            
            // Hacer el preview clicable para abrir el selector de color
            $(document).off('click', '#color-preview').on('click', '#color-preview', function() {
                $('.custom-color-picker').click();
            });
        }

        // Función para ver lecciones de un curso
        function viewCourseLessons(courseId, courseName) {
            window.location.href = '/dashboard/courses/' + courseId + '/lessons?courseName=' + encodeURIComponent(courseName);
        }

        // Función para validar color hexadecimal
        function isValidHexColor(color) {
            return /^#[0-9A-F]{6}$/i.test(color);
        }

        // Función para mostrar un modal de añadir curso (fallback para DataTable)
        function showAddCourseModal() {
            // Esta función se puede expandir si usamos DataTable en lugar de jTable
            alert('Funcionalidad de agregar curso (implementar modal personalizado si se usa DataTable)');
        }

        // Funciones de edición y eliminación para DataTable
        function editCourse(id) {
            alert('Editar curso ' + id + ' (implementar modal de edición)');
        }

        function deleteCourse(id) {
            if (confirm('¿Estás seguro de que quieres eliminar este curso?')) {
                $.ajax({
                    url: '/api/courses/' + id,
                    type: 'DELETE',
                    success: function(response) {
                        alert('Curso eliminado exitosamente');
                        location.reload(); // Recargar la página
                    },
                    error: function(xhr) {
                        alert('Error al eliminar curso: ' + (xhr.responseJSON?.message || xhr.statusText));
                    }
                });
            }
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