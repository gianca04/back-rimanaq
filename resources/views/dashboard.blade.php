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
                <h1>Sistema Rimanaq - Backend Listo</h1>
                <p class="text-muted">API configurada y lista para usar. Campo 'content' agregado al modelo Lesson.</p>
                
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-success">
                            <h4><i class="fas fa-check-circle me-2"></i>✅ Backend Completado</h4>
                            <p class="mb-0">La API está configurada y lista. El campo 'content' (JSON) fue agregado exitosamente al modelo Lesson.</p>
                        </div>
                        
                        <div class="card">
                            <div class="card-header">
                                <h5>🔌 Endpoints API Disponibles</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Autenticación:</h6>
                                        <ul class="list-unstyled">
                                            <li>• POST /api/login</li>
                                            <li>• POST /api/logout</li>
                                            <li>• POST /api/register</li>
                                        </ul>
                                        
                                        <h6>Cursos:</h6>
                                        <ul class="list-unstyled">
                                            <li>• GET/POST/PUT/DELETE /api/courses</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Lecciones (con campo 'content'):</h6>
                                        <ul class="list-unstyled">
                                            <li>• GET/POST/PUT/DELETE /api/lessons</li>
                                        </ul>
                                        
                                        <h6>Gestos & Progreso:</h6>
                                        <ul class="list-unstyled">
                                            <li>• GET/POST/PUT/DELETE /api/gestures</li>
                                            <li>• GET/POST/PUT/DELETE /api/progress</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="alert alert-info mt-3">
                                    <strong>Nuevo:</strong> El modelo Lesson incluye campo <code>content</code> (JSON nullable) para contenido estructurado.
                                </div>
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