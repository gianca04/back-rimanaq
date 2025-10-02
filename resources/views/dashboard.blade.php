<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - Rimanaq</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
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
        .card {
            margin-bottom: 20px;
        }
        .nav-link {
            margin-right: 10px;
        }
        .logout-btn {
            margin-left: auto;
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
                <h1>Dashboard de Administración</h1>
                <p class="text-muted">Gestiona los cursos, lecciones, gestos y progreso de la plataforma Rimanaq.</p>
                
                <!-- Flujo jerárquico explicativo -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-info" role="alert">
                            <h5 class="alert-heading">🎯 Flujo de Navegación</h5>
                            <p class="mb-0">
                                <strong>Cursos</strong> → <strong>Lecciones</strong> → <strong>Gestos</strong><br>
                                <small>Haz clic en "Ver Lecciones" dentro de un curso para gestionar sus lecciones específicas, y luego en "Ver Gestos" para gestionar los gestos de cada lección.</small>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 col-sm-6">
                        <div class="card text-center border-primary">
                            <div class="card-body">
                                <div class="text-primary mb-2">
                                    <i class="fas fa-book" style="font-size: 2rem;"></i>
                                </div>
                                <h5 class="card-title">1. Cursos</h5>
                                <p class="card-text">Gestiona los cursos disponibles. Desde aquí puedes ver las lecciones de cada curso.</p>
                                <a href="/dashboard/courses" class="btn btn-primary">Gestionar Cursos</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6">
                        <div class="card text-center border-success">
                            <div class="card-body">
                                <div class="text-success mb-2">
                                    <i class="fas fa-list" style="font-size: 2rem;"></i>
                                </div>
                                <h5 class="card-title">2. Lecciones</h5>
                                <p class="card-text">Ve todas las lecciones o accede desde un curso específico.</p>
                                <a href="/dashboard/lessons" class="btn btn-success">Ver Todas las Lecciones</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6">
                        <div class="card text-center border-warning">
                            <div class="card-body">
                                <div class="text-warning mb-2">
                                    <i class="fas fa-hand-paper" style="font-size: 2rem;"></i>
                                </div>
                                <h5 class="card-title">3. Gestos</h5>
                                <p class="card-text">Ve todos los gestos o accede desde una lección específica.</p>
                                <a href="/dashboard/gestures" class="btn btn-warning">Ver Todos los Gestos</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6">
                        <div class="card text-center border-info">
                            <div class="card-body">
                                <div class="text-info mb-2">
                                    <i class="fas fa-chart-line" style="font-size: 2rem;"></i>
                                </div>
                                <h5 class="card-title">Progreso</h5>
                                <p class="card-text">Monitorea el progreso de los usuarios en las lecciones.</p>
                                <a href="/dashboard/progress" class="btn btn-info">Ver Progreso</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Configurar CSRF token para todas las peticiones
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token')
            }
        });

        // Función de logout
        function logout() {
            if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
                // Hacer petición de logout
                $.ajax({
                    url: '/api/logout',
                    method: 'POST',
                    success: function() {
                        // Limpiar localStorage
                        localStorage.removeItem('auth_token');
                        localStorage.removeItem('user_data');
                        // Redirigir al login
                        window.location.href = '/';
                    },
                    error: function() {
                        // Aunque falle la petición, limpiar localStorage y redirigir
                        localStorage.removeItem('auth_token');
                        localStorage.removeItem('user_data');
                        window.location.href = '/';
                    }
                });
            }
        }

        // Verificar si hay token al cargar la página
        $(document).ready(function() {
            const token = localStorage.getItem('auth_token');
            if (!token) {
                window.location.href = '/';
            }
        });
    </script>
</body>
</html>