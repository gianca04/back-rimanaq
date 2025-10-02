<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Lecciones - Rimanaq</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- jTable CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jtable/2.5.0/themes/lightcolor/blue/jtable.min.css">
    
    <!-- jQuery UI CSS -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/ui-lightness/jquery-ui.css">
    
    <style>
        :root {
            --primary-color: #0d6efd;
            --success-color: #198754;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --light-bg: #f8f9fa;
        }

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

        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #0056b3 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0.5rem;
        }

        .page-header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 300;
        }

        .page-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
        }

        .stats-card {
            background: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border-left: 4px solid var(--primary-color);
        }

        .stats-card h3 {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }

        .stats-card p {
            color: #6c757d;
            margin: 0;
        }

        /* Mejoras para jTable */
        .jtable-main-container {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .jtable-title {
            background: var(--primary-color);
            color: white;
            font-weight: 600;
        }

        .jtable-toolbar {
            background: var(--light-bg);
            border-bottom: 1px solid #dee2e6;
        }

        .jtable-data-row:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }

        /* Estilos para botones de dificultad */
        .difficulty-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .difficulty-facil {
            background-color: rgba(25, 135, 84, 0.1);
            color: var(--success-color);
        }

        .difficulty-intermedio {
            background-color: rgba(255, 193, 7, 0.1);
            color: #b45309;
        }

        .difficulty-dificil {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger-color);
        }

        /* Mejorar formularios */
        .jtable-input-field-container input,
        .jtable-input-field-container select,
        .jtable-input-field-container textarea {
            width: 100% !important;
            border-radius: 0.375rem !important;
            border: 1px solid #ced4da !important;
            padding: 0.375rem 0.75rem !important;
        }

        .jtable-input-field-container select:focus,
        .jtable-input-field-container input:focus,
        .jtable-input-field-container textarea:focus {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25) !important;
        }

        /* Loading spinner */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .spinner-border-lg {
            width: 3rem;
            height: 3rem;
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 2rem;
            }
            
            .jtable-main-container {
                margin: 0 -15px;
            }
        }

        /* Animation for success messages */
        .alert-success {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="text-center">
            <div class="spinner-border spinner-border-lg text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2 text-muted">Procesando...</p>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
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
        <!-- Page Header -->
        <div class="page-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1><i class="fas fa-list-alt me-3"></i>Gestión de Lecciones</h1>
                        <p>Administra las lecciones de todos los cursos desde un solo lugar</p>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card text-dark">
                            <h3 id="totalLessons">0</h3>
                            <p>Lecciones totales</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerts Container -->
        <div id="alertsContainer"></div>

        <!-- Main Table -->
        <div class="row">
            <div class="col-12">
                <div id="LessonsTableContainer"></div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jtable/2.5.0/jquery.jtable.min.js"></script>

    <script>
        // Configuration object for better organization
        const AppConfig = {
            api: {
                baseUrl: '/api',
                endpoints: {
                    lessons: '/api/lessons',
                    courses: '/api/courses',
                    logout: '/api/logout'
                }
            },
            ui: {
                pageSize: 15,
                animationDuration: 300
            }
        };

        // Application state
        const AppState = {
            coursesOptions: {},
            currentUser: null,
            isLoading: false
        };

        // Utility functions
        const Utils = {
            showLoading: function() {
                AppState.isLoading = true;
                $('#loadingOverlay').show();
            },

            hideLoading: function() {
                AppState.isLoading = false;
                $('#loadingOverlay').hide();
            },

            showAlert: function(message, type = 'success', duration = 5000) {
                const alertId = 'alert_' + Date.now();
                const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show" id="${alertId}" role="alert">
                        <i class="fas fa-${this.getAlertIcon(type)} me-2"></i>
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                
                $('#alertsContainer').prepend(alertHtml);
                
                if (duration > 0) {
                    setTimeout(() => {
                        $(`#${alertId}`).alert('close');
                    }, duration);
                }
            },

            getAlertIcon: function(type) {
                const icons = {
                    'success': 'check-circle',
                    'danger': 'exclamation-triangle',
                    'warning': 'exclamation-circle',
                    'info': 'info-circle'
                };
                return icons[type] || 'info-circle';
            },

            formatDifficulty: function(difficulty) {
                const badges = {
                    'fácil': '<span class="difficulty-badge difficulty-facil">Fácil</span>',
                    'intermedio': '<span class="difficulty-badge difficulty-intermedio">Intermedio</span>',
                    'difícil': '<span class="difficulty-badge difficulty-dificil">Difícil</span>'
                };
                return badges[difficulty] || difficulty;
            },

            formatDuration: function(minutes) {
                if (minutes < 60) {
                    return `${minutes} min`;
                }
                
                const hours = Math.floor(minutes / 60);
                const remainingMinutes = minutes % 60;
                
                if (remainingMinutes === 0) {
                    return hours === 1 ? "1 hora" : `${hours} horas`;
                }
                
                return hours === 1 
                    ? `1 hora ${remainingMinutes} min`
                    : `${hours} horas ${remainingMinutes} min`;
            }
        };

        // API functions
        const API = {
            request: function(url, options = {}) {
                const defaultOptions = {
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                        'Accept': 'application/json'
                    }
                };

                return $.ajax({
                    url: url,
                    dataType: 'json',
                    ...defaultOptions,
                    ...options
                });
            },

            loadCourses: function() {
                return this.request(AppConfig.api.endpoints.courses)
                    .then(function(response) {
                        AppState.coursesOptions = {};
                        console.log('Cursos recibidos:', response);
                        
                        if (response.data && response.data.length > 0) {
                            response.data.forEach(function(course) {
                                AppState.coursesOptions[course.id] = course.name;
                            });
                            return Promise.resolve();
                        } else {
                            Utils.showAlert('Advertencia: No se encontraron cursos. Crea al menos un curso primero.', 'warning');
                            return Promise.reject('No courses found');
                        }
                    })
                    .catch(function(xhr) {
                        console.error('Error cargando cursos:', xhr);
                        const message = xhr.responseJSON?.message || 'Error al cargar cursos';
                        Utils.showAlert(`Error al cargar cursos: ${message}`, 'danger');
                        return Promise.reject(xhr);
                    });
            }
        };

        // jTable configuration
        const TableConfig = {
            getConfig: function() {
                return {
                    title: 'Gestión de Lecciones',
                    paging: true,
                    pageSize: AppConfig.ui.pageSize,
                    sorting: true,
                    defaultSorting: 'course_id ASC, level_number ASC',
                    selectOnLoad: false,

                    actions: {
                        listAction: this.getListAction(),
                        createAction: this.getCreateAction(),
                        updateAction: this.getUpdateAction(),
                        deleteAction: this.getDeleteAction()
                    },

                    fields: this.getFields(),

                    // Event handlers
                    recordsLoaded: function(event, data) {
                        $('#totalLessons').text(data.records.length);
                        Utils.hideLoading();
                    },

                    formCreated: function(event, data) {
                        console.log('Formulario creado:', data.formType);
                        
                        // Aplicar estilos Bootstrap a los elementos del formulario
                        setTimeout(function() {
                            data.form.find('input, select, textarea').addClass('form-control');
                            data.form.find('select').removeClass('form-control').addClass('form-select');
                        }, 50);
                    },

                    recordAdded: function(event, data) {
                        Utils.showAlert('Lección creada exitosamente', 'success');
                    },

                    recordUpdated: function(event, data) {
                        Utils.showAlert('Lección actualizada exitosamente', 'success');
                    },

                    recordDeleted: function(event, data) {
                        Utils.showAlert('Lección eliminada exitosamente', 'success');
                    }
                };
            },

            getListAction: function() {
                return function(postData) {
                    Utils.showLoading();
                    return API.request(AppConfig.api.endpoints.lessons)
                        .then(function(response) {
                            return {
                                Result: 'OK',
                                Records: response.data || [],
                                TotalRecordCount: (response.data || []).length
                            };
                        })
                        .catch(function(xhr) {
                            Utils.hideLoading();
                            const message = xhr.responseJSON?.message || 'Error al cargar lecciones';
                            Utils.showAlert(`Error: ${message}`, 'danger');
                            return {
                                Result: 'ERROR',
                                Message: message
                            };
                        });
                };
            },

            getCreateAction: function() {
                return function(postData) {
                    console.log('Creando lección:', postData);
                    
                    // Validaciones del lado del cliente
                    if (!postData.course_id || postData.course_id === '0') {
                        Utils.showAlert('Debe seleccionar un curso', 'danger');
                        return Promise.resolve({
                            Result: 'ERROR',
                            Message: 'Debe seleccionar un curso'
                        });
                    }

                    // Convertir a números
                    postData.course_id = parseInt(postData.course_id) || 0;
                    postData.level_number = parseInt(postData.level_number) || 1;
                    postData.time_minutes = parseInt(postData.time_minutes) || 30;
                    
                    Utils.showLoading();
                    return API.request(AppConfig.api.endpoints.lessons, {
                        type: 'POST',
                        data: postData,
                        contentType: 'application/x-www-form-urlencoded'
                    })
                    .then(function(response) {
                        Utils.hideLoading();
                        return {
                            Result: 'OK',
                            Record: response.data
                        };
                    })
                    .catch(function(xhr) {
                        Utils.hideLoading();
                        const response = xhr.responseJSON;
                        const message = response?.message || 'Error al crear lección';
                        
                        if (response?.errors) {
                            // Mostrar errores específicos de validación
                            const errorMessages = Object.values(response.errors).flat();
                            Utils.showAlert(`Errores de validación: ${errorMessages.join(', ')}`, 'danger');
                        } else {
                            Utils.showAlert(`Error: ${message}`, 'danger');
                        }
                        
                        return {
                            Result: 'ERROR',
                            Message: message
                        };
                    });
                };
            },

            getUpdateAction: function() {
                return function(postData) {
                    console.log('Actualizando lección:', postData);
                    
                    // Convertir a números
                    if (postData.course_id) postData.course_id = parseInt(postData.course_id);
                    if (postData.level_number) postData.level_number = parseInt(postData.level_number);
                    if (postData.time_minutes) postData.time_minutes = parseInt(postData.time_minutes);
                    
                    Utils.showLoading();
                    return API.request(`${AppConfig.api.endpoints.lessons}/${postData.id}`, {
                        type: 'PUT',
                        data: postData,
                        contentType: 'application/x-www-form-urlencoded'
                    })
                    .then(function(response) {
                        Utils.hideLoading();
                        return {
                            Result: 'OK',
                            Record: response.data
                        };
                    })
                    .catch(function(xhr) {
                        Utils.hideLoading();
                        const response = xhr.responseJSON;
                        const message = response?.message || 'Error al actualizar lección';
                        
                        if (response?.errors) {
                            const errorMessages = Object.values(response.errors).flat();
                            Utils.showAlert(`Errores de validación: ${errorMessages.join(', ')}`, 'danger');
                        } else {
                            Utils.showAlert(`Error: ${message}`, 'danger');
                        }
                        
                        return {
                            Result: 'ERROR',
                            Message: message
                        };
                    });
                };
            },

            getDeleteAction: function() {
                return function(postData) {
                    Utils.showLoading();
                    return API.request(`${AppConfig.api.endpoints.lessons}/${postData.id}`, {
                        type: 'DELETE'
                    })
                    .then(function(response) {
                        Utils.hideLoading();
                        return {
                            Result: 'OK'
                        };
                    })
                    .catch(function(xhr) {
                        Utils.hideLoading();
                        const response = xhr.responseJSON;
                        const message = response?.message || 'Error al eliminar lección';
                        
                        if (response?.errors) {
                            const errorMessages = Object.values(response.errors).flat();
                            Utils.showAlert(`No se puede eliminar: ${errorMessages.join(', ')}`, 'warning');
                        } else {
                            Utils.showAlert(`Error: ${message}`, 'danger');
                        }
                        
                        return {
                            Result: 'ERROR',
                            Message: message
                        };
                    });
                };
            },

            getFields: function() {
                return {
                    id: {
                        key: true,
                        create: false,
                        edit: false,
                        list: true,
                        title: 'ID',
                        width: '3%'
                    },
                    name: {
                        title: 'Nombre',
                        width: '20%',
                        inputClass: 'form-control',
                        inputTitle: 'Ingrese el nombre de la lección (mínimo 3 caracteres)'
                    },
                    description: {
                        title: 'Descripción',
                        width: '25%',
                        type: 'textarea',
                        inputClass: 'form-control',
                        inputTitle: 'Descripción detallada de la lección (mínimo 10 caracteres)'
                    },
                    course_id: {
                        title: 'Curso',
                        width: '15%',
                        type: 'option',
                        options: AppState.coursesOptions,
                        defaultValue: Object.keys(AppState.coursesOptions)[0] || '',
                        display: function(data) {
                            return AppState.coursesOptions[data.record.course_id] || 'N/A';
                        }
                    },
                    level_number: {
                        title: 'Nivel',
                        width: '8%',
                        type: 'number',
                        inputClass: 'form-control',
                        defaultValue: 1,
                        inputTitle: 'Número de nivel (1-100)'
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
                        inputClass: 'form-select',
                        display: function(data) {
                            return Utils.formatDifficulty(data.record.difficulty);
                        }
                    },
                    time_minutes: {
                        title: 'Duración',
                        width: '10%',
                        type: 'number',
                        inputClass: 'form-control',
                        defaultValue: 30,
                        inputTitle: 'Duración en minutos (5-600)',
                        display: function(data) {
                            return Utils.formatDuration(data.record.time_minutes);
                        }
                    },
                    Actions: {
                        title: 'Gestos',
                        width: '7%',
                        sorting: false,
                        edit: false,
                        create: false,
                        display: function(data) {
                            return `<button type="button" class="btn btn-sm btn-success" onclick="viewLessonGestures(${data.record.id}, '${data.record.name.replace(/'/g, "\\'")}')">
                                        <i class="fas fa-hand-paper me-1"></i>
                                        Ver
                                    </button>`;
                        }
                    }
                };
            }
        };

        // Application initialization
        const App = {
            init: function() {
                console.log('Inicializando aplicación...');
                
                // Verificar autenticación
                if (!this.checkAuth()) return;
                
                // Configurar AJAX
                this.setupAjax();
                
                // Cargar datos iniciales y inicializar tabla
                API.loadCourses()
                    .then(() => {
                        this.initializeTable();
                    })
                    .catch((error) => {
                        console.error('Error en inicialización:', error);
                        Utils.showAlert('Error al inicializar la aplicación', 'danger');
                    });
            },

            checkAuth: function() {
                const token = localStorage.getItem('auth_token');
                if (!token) {
                    Utils.showAlert('Sesión expirada. Redirigiendo al login...', 'warning');
                    setTimeout(() => {
                        window.location.href = '/';
                    }, 2000);
                    return false;
                }
                return true;
            },

            setupAjax: function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Authorization': 'Bearer ' + localStorage.getItem('auth_token')
                    }
                });
            },

            initializeTable: function() {
                console.log('Inicializando tabla con opciones de cursos:', AppState.coursesOptions);
                $('#LessonsTableContainer').jtable(TableConfig.getConfig());
                $('#LessonsTableContainer').jtable('load');
            }
        };

        // Global functions for compatibility
        function viewLessonGestures(lessonId, lessonName) {
            const encodedName = encodeURIComponent(lessonName);
            window.location.href = `/dashboard/lessons/${lessonId}/gestures?lessonName=${encodedName}`;
        }

        function logout() {
            if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
                Utils.showLoading();
                $.ajax({
                    url: AppConfig.api.endpoints.logout,
                    method: 'POST',
                    success: function() {
                        localStorage.removeItem('auth_token');
                        localStorage.removeItem('user_data');
                        Utils.showAlert('Sesión cerrada exitosamente', 'success');
                        setTimeout(() => {
                            window.location.href = '/';
                        }, 1000);
                    },
                    error: function() {
                        localStorage.removeItem('auth_token');
                        localStorage.removeItem('user_data');
                        window.location.href = '/';
                    },
                    complete: function() {
                        Utils.hideLoading();
                    }
                });
            }
        }

        // Document ready
        $(document).ready(function() {
            App.init();
        });
    </script>
</body>
</html>