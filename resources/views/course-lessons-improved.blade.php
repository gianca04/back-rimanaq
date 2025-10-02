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
        :root {
            --primary-color: #0d6efd;
            --success-color: #198754;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --light-bg: #f8f9fa;
            --border-radius: 0.5rem;
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

        .breadcrumb {
            background: linear-gradient(135deg, var(--light-bg) 0%, #e9ecef 100%);
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: ">";
            font-weight: bold;
            color: var(--primary-color);
        }

        .course-info {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            padding: 2rem;
            border-radius: var(--border-radius);
            margin-bottom: 2rem;
            border-left: 6px solid var(--primary-color);
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
        }

        .course-info h4 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .course-info p {
            color: #1976d2;
            margin: 0;
            font-size: 1.1rem;
        }

        .stats-row {
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
            border-top: 4px solid var(--primary-color);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .stat-card .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .stat-card .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card.stat-success {
            border-top-color: var(--success-color);
        }

        .stat-card.stat-success .stat-number {
            color: var(--success-color);
        }

        .stat-card.stat-warning {
            border-top-color: var(--warning-color);
        }

        .stat-card.stat-warning .stat-number {
            color: #b45309;
        }

        .stat-card.stat-danger {
            border-top-color: var(--danger-color);
        }

        .stat-card.stat-danger .stat-number {
            color: var(--danger-color);
        }

        /* jTable improvements */
        .jtable-main-container {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            border-radius: var(--border-radius);
            overflow: hidden;
            border: none;
        }

        .jtable-title {
            background: linear-gradient(135deg, var(--primary-color) 0%, #0056b3 100%);
            color: white;
            font-weight: 600;
            padding: 1rem 1.5rem;
            font-size: 1.1rem;
        }

        .jtable-toolbar {
            background: var(--light-bg);
            border-bottom: 1px solid #dee2e6;
            padding: 1rem 1.5rem;
        }

        .jtable-data-row:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }

        .jtable-column-header {
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }

        /* Difficulty badges */
        .difficulty-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .difficulty-facil {
            background-color: rgba(25, 135, 84, 0.15);
            color: var(--success-color);
            border: 1px solid rgba(25, 135, 84, 0.3);
        }

        .difficulty-intermedio {
            background-color: rgba(255, 193, 7, 0.15);
            color: #b45309;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .difficulty-dificil {
            background-color: rgba(220, 53, 69, 0.15);
            color: var(--danger-color);
            border: 1px solid rgba(220, 53, 69, 0.3);
        }

        /* Duration display */
        .duration-badge {
            background-color: rgba(108, 117, 125, 0.15);
            color: #495057;
            padding: 0.3rem 0.6rem;
            border-radius: 0.5rem;
            font-size: 0.8rem;
            font-weight: 500;
        }

        /* Action buttons */
        .btn-gestures {
            background: linear-gradient(135deg, var(--success-color) 0%, #146c43 100%);
            border: none;
            color: white;
            font-weight: 500;
            padding: 0.4rem 1rem;
            border-radius: 0.375rem;
            transition: all 0.2s ease;
        }

        .btn-gestures:hover {
            transform: translateY(-1px);
            box-shadow: 0 0.25rem 0.5rem rgba(25, 135, 84, 0.3);
            color: white;
        }

        /* Form improvements */
        .jtable-input-field-container input,
        .jtable-input-field-container select,
        .jtable-input-field-container textarea {
            width: 100% !important;
            border-radius: 0.375rem !important;
            border: 2px solid #e9ecef !important;
            padding: 0.5rem 0.75rem !important;
            font-size: 0.9rem !important;
            transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
        }

        .jtable-input-field-container input:focus,
        .jtable-input-field-container select:focus,
        .jtable-input-field-container textarea:focus {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15) !important;
            outline: none !important;
        }

        /* Loading states */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.95);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            backdrop-filter: blur(2px);
        }

        .spinner-border-lg {
            width: 3rem;
            height: 3rem;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
        }

        .empty-state i {
            font-size: 4rem;
            color: #dee2e6;
            margin-bottom: 1rem;
        }

        /* Animations */
        .fade-in {
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .course-info {
                padding: 1.5rem 1rem;
            }
            
            .stat-card {
                margin-bottom: 1rem;
            }
            
            .jtable-main-container {
                margin: 0 -0.5rem;
            }
            
            .breadcrumb {
                padding: 0.75rem 1rem;
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
            <p class="mt-3 text-muted fw-medium">Cargando lecciones...</p>
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
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb" class="fade-in">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="/dashboard" class="text-decoration-none">
                        <i class="fas fa-home me-1"></i>
                        Dashboard
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="/dashboard/courses" class="text-decoration-none">
                        <i class="fas fa-book me-1"></i>
                        Cursos
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fas fa-list-alt me-1"></i>
                    Lecciones del Curso
                </li>
            </ol>
        </nav>

        <!-- Course Information -->
        <div class="course-info fade-in">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 id="course-title">
                        <i class="fas fa-book-open me-2"></i>
                        Cargando información del curso...
                    </h4>
                    <p id="course-description">Gestiona las lecciones de este curso específico</p>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-3 justify-content-md-end">
                        <a href="/dashboard/lessons" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Ver Todas
                        </a>
                        <button class="btn btn-primary" onclick="refreshData()">
                            <i class="fas fa-sync-alt me-1"></i>
                            Actualizar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Row -->
        <div class="stats-row fade-in">
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-number" id="totalLessons">0</div>
                        <div class="stat-label">Total Lecciones</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card stat-success">
                        <div class="stat-number" id="easyLessons">0</div>
                        <div class="stat-label">Fáciles</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card stat-warning">
                        <div class="stat-number" id="intermediateLessons">0</div>
                        <div class="stat-label">Intermedias</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card stat-danger">
                        <div class="stat-number" id="hardLessons">0</div>
                        <div class="stat-label">Difíciles</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerts Container -->
        <div id="alertsContainer"></div>

        <!-- Main Table -->
        <div class="row">
            <div class="col-12">
                <div id="LessonsTableContainer" class="fade-in"></div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jtable/2.5.0/jquery.jtable.min.js"></script>

    <script>
        // Application Configuration
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

        // Application State
        const AppState = {
            courseId: null,
            courseName: '',
            courseData: null,
            lessons: [],
            isLoading: false
        };

        // Utility Functions
        const Utils = {
            showLoading: function(message = 'Procesando...') {
                AppState.isLoading = true;
                $('#loadingOverlay p').text(message);
                $('#loadingOverlay').fadeIn(200);
            },

            hideLoading: function() {
                AppState.isLoading = false;
                $('#loadingOverlay').fadeOut(200);
            },

            showAlert: function(message, type = 'success', duration = 5000) {
                const alertId = 'alert_' + Date.now();
                const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show" id="${alertId}" role="alert">
                        <i class="fas fa-${this.getAlertIcon(type)} me-2"></i>
                        <strong>${this.getAlertTitle(type)}:</strong> ${message}
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

            getAlertTitle: function(type) {
                const titles = {
                    'success': 'Éxito',
                    'danger': 'Error',
                    'warning': 'Advertencia',
                    'info': 'Información'
                };
                return titles[type] || 'Notificación';
            },

            formatDifficulty: function(difficulty) {
                const badges = {
                    'fácil': '<span class="difficulty-badge difficulty-facil"><i class="fas fa-circle"></i> Fácil</span>',
                    'intermedio': '<span class="difficulty-badge difficulty-intermedio"><i class="fas fa-circle"></i> Intermedio</span>',
                    'difícil': '<span class="difficulty-badge difficulty-dificil"><i class="fas fa-circle"></i> Difícil</span>'
                };
                return badges[difficulty] || difficulty;
            },

            formatDuration: function(minutes) {
                const formatted = this.getFormattedDuration(minutes);
                return `<span class="duration-badge"><i class="fas fa-clock me-1"></i>${formatted}</span>`;
            },

            getFormattedDuration: function(minutes) {
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
            },

            updateStatistics: function(lessons) {
                const stats = {
                    total: lessons.length,
                    easy: lessons.filter(l => l.difficulty === 'fácil').length,
                    intermediate: lessons.filter(l => l.difficulty === 'intermedio').length,
                    hard: lessons.filter(l => l.difficulty === 'difícil').length
                };

                $('#totalLessons').text(stats.total);
                $('#easyLessons').text(stats.easy);
                $('#intermediateLessons').text(stats.intermediate);
                $('#hardLessons').text(stats.hard);
            }
        };

        // API Functions
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

            getCourseInfo: function(courseId) {
                return this.request(`${AppConfig.api.endpoints.courses}/${courseId}`);
            },

            getCourseLessons: function(courseId) {
                return this.request(`${AppConfig.api.endpoints.courses}/${courseId}/lessons`);
            }
        };

        // jTable Configuration
        const TableConfig = {
            getConfig: function() {
                return {
                    title: `Lecciones de: ${AppState.courseName}`,
                    paging: true,
                    pageSize: AppConfig.ui.pageSize,
                    sorting: true,
                    defaultSorting: 'level_number ASC',
                    selectOnLoad: false,

                    actions: {
                        listAction: this.getListAction(),
                        createAction: this.getCreateAction(),
                        updateAction: this.getUpdateAction(),
                        deleteAction: this.getDeleteAction()
                    },

                    fields: this.getFields(),

                    recordsLoaded: function(event, data) {
                        AppState.lessons = data.records;
                        Utils.updateStatistics(data.records);
                        Utils.hideLoading();
                        
                        if (data.records.length === 0) {
                            setTimeout(() => {
                                $('#LessonsTableContainer').after(`
                                    <div class="empty-state mt-4">
                                        <i class="fas fa-book-open"></i>
                                        <h5 class="mt-3">No hay lecciones en este curso</h5>
                                        <p class="text-muted">Agrega la primera lección haciendo clic en "Agregar nuevo registro"</p>
                                    </div>
                                `);
                            }, 100);
                        }
                    },

                    formCreated: function(event, data) {
                        console.log('Formulario creado:', data.formType);
                        
                        setTimeout(function() {
                            data.form.find('input, select, textarea').addClass('form-control');
                            data.form.find('select').removeClass('form-control').addClass('form-select');
                            
                            // Agregar ayuda contextual
                            data.form.find('input[name="name"]').attr('placeholder', 'Ej: Saludos básicos');
                            data.form.find('textarea[name="description"]').attr('placeholder', 'Descripción detallada de la lección...');
                            data.form.find('input[name="level_number"]').attr('placeholder', '1');
                            data.form.find('input[name="time_minutes"]').attr('placeholder', '30');
                        }, 50);
                    },

                    recordAdded: function(event, data) {
                        Utils.showAlert('Lección creada exitosamente', 'success');
                        refreshData();
                    },

                    recordUpdated: function(event, data) {
                        Utils.showAlert('Lección actualizada exitosamente', 'success');
                        refreshData();
                    },

                    recordDeleted: function(event, data) {
                        Utils.showAlert('Lección eliminada exitosamente', 'success');
                        refreshData();
                    }
                };
            },

            getListAction: function() {
                return function(postData) {
                    Utils.showLoading('Cargando lecciones...');
                    
                    return API.getCourseLessons(AppState.courseId)
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
                            Utils.showAlert(message, 'danger');
                            return {
                                Result: 'ERROR',
                                Message: message
                            };
                        });
                };
            },

            getCreateAction: function() {
                return function(postData) {
                    // Agregar automáticamente el course_id
                    postData.course_id = AppState.courseId;
                    
                    // Convertir a números
                    postData.level_number = parseInt(postData.level_number) || 1;
                    postData.time_minutes = parseInt(postData.time_minutes) || 30;
                    
                    Utils.showLoading('Creando lección...');
                    
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
                            const errorMessages = Object.values(response.errors).flat();
                            Utils.showAlert(`Errores de validación: ${errorMessages.join(', ')}`, 'danger');
                        } else {
                            Utils.showAlert(message, 'danger');
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
                    // Mantener el course_id original
                    postData.course_id = AppState.courseId;
                    
                    // Convertir a números
                    if (postData.level_number) postData.level_number = parseInt(postData.level_number);
                    if (postData.time_minutes) postData.time_minutes = parseInt(postData.time_minutes);
                    
                    Utils.showLoading('Actualizando lección...');
                    
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
                            Utils.showAlert(`Errores: ${errorMessages.join(', ')}`, 'danger');
                        } else {
                            Utils.showAlert(message, 'danger');
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
                    Utils.showLoading('Eliminando lección...');
                    
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
                            Utils.showAlert(message, 'danger');
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
                        width: '4%'
                    },
                    level_number: {
                        title: 'Nivel',
                        width: '8%',
                        type: 'number',
                        inputClass: 'form-control',
                        defaultValue: function() {
                            // Calcular el siguiente nivel disponible
                            const maxLevel = Math.max(...AppState.lessons.map(l => l.level_number || 0));
                            return maxLevel + 1;
                        },
                        inputTitle: 'Número de nivel único para este curso'
                    },
                    name: {
                        title: 'Nombre de la Lección',
                        width: '25%',
                        inputClass: 'form-control',
                        inputTitle: 'Nombre descriptivo de la lección'
                    },
                    description: {
                        title: 'Descripción',
                        width: '30%',
                        type: 'textarea',
                        inputClass: 'form-control',
                        inputTitle: 'Descripción detallada del contenido'
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
                        inputTitle: 'Duración estimada en minutos',
                        display: function(data) {
                            return Utils.formatDuration(data.record.time_minutes);
                        }
                    },
                    Actions: {
                        title: 'Gestos',
                        width: '11%',
                        sorting: false,
                        edit: false,
                        create: false,
                        display: function(data) {
                            return `<button type="button" class="btn btn-gestures btn-sm" onclick="viewLessonGestures(${data.record.id}, '${data.record.name.replace(/'/g, "\\'")}')">
                                        <i class="fas fa-hand-paper me-1"></i>
                                        Ver Gestos
                                    </button>`;
                        }
                    }
                };
            }
        };

        // Application Functions
        const App = {
            init: function() {
                console.log('Inicializando aplicación...');
                
                if (!this.checkAuth()) return;
                
                this.setupAjax();
                this.extractRouteParams();
                this.loadCourseData();
            },

            checkAuth: function() {
                const token = localStorage.getItem('auth_token');
                if (!token) {
                    Utils.showAlert('Sesión expirada. Redirigiendo...', 'warning');
                    setTimeout(() => window.location.href = '/', 2000);
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

            extractRouteParams: function() {
                const pathParts = window.location.pathname.split('/');
                AppState.courseId = pathParts[3]; // /dashboard/courses/{id}/lessons

                const urlParams = new URLSearchParams(window.location.search);
                AppState.courseName = urlParams.get('courseName') || 'Curso';

                console.log('Parámetros extraídos:', {
                    courseId: AppState.courseId,
                    courseName: AppState.courseName
                });
            },

            loadCourseData: function() {
                Utils.showLoading('Cargando información del curso...');
                
                // Cargar información básica del curso desde la URL
                this.updateCourseInfo();
                
                // Cargar información completa del curso
                API.getCourseInfo(AppState.courseId)
                    .then((response) => {
                        AppState.courseData = response.data;
                        this.updateCourseInfoComplete();
                        this.initializeTable();
                    })
                    .catch((xhr) => {
                        console.error('Error cargando curso:', xhr);
                        Utils.showAlert('Error al cargar la información del curso', 'danger');
                        this.initializeTable(); // Inicializar tabla de todas formas
                    });
            },

            updateCourseInfo: function() {
                document.getElementById('course-title').innerHTML = 
                    `<i class="fas fa-book-open me-2"></i>Lecciones de: ${AppState.courseName}`;
            },

            updateCourseInfoComplete: function() {
                if (AppState.courseData) {
                    const description = AppState.courseData.description || 'Gestiona las lecciones de este curso específico';
                    document.getElementById('course-description').textContent = description;
                }
            },

            initializeTable: function() {
                console.log('Inicializando tabla para curso:', AppState.courseId);
                $('#LessonsTableContainer').jtable(TableConfig.getConfig());
                $('#LessonsTableContainer').jtable('load');
            }
        };

        // Global Functions
        function viewLessonGestures(lessonId, lessonName) {
            const params = new URLSearchParams({
                lessonName: lessonName,
                courseId: AppState.courseId,
                courseName: AppState.courseName
            });
            
            window.location.href = `/dashboard/lessons/${lessonId}/gestures?${params.toString()}`;
        }

        function refreshData() {
            Utils.showLoading('Actualizando datos...');
            $('#LessonsTableContainer').jtable('reload');
        }

        function logout() {
            if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
                Utils.showLoading('Cerrando sesión...');
                
                $.ajax({
                    url: AppConfig.api.endpoints.logout,
                    method: 'POST',
                    success: function() {
                        localStorage.removeItem('auth_token');
                        localStorage.removeItem('user_data');
                        Utils.showAlert('Sesión cerrada exitosamente', 'success');
                        setTimeout(() => window.location.href = '/', 1000);
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

        // Document Ready
        $(document).ready(function() {
            App.init();
        });
    </script>
</body>
</html>