<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />

    <title>Dashboard - Rimanaq</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            color: #333;
        }

        .header {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo h1 {
            color: #667eea;
            font-size: 1.5rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background: #5a6fd8;
        }

        .btn-danger {
            background: #d63031;
        }

        .btn-danger:hover {
            background: #b71c1c;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .welcome {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .welcome h2 {
            color: #333;
            margin-bottom: 1rem;
        }

        .welcome p {
            color: #666;
            line-height: 1.6;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .stat-card h3 {
            color: #667eea;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .stat-card p {
            color: #666;
            font-weight: bold;
        }

        .actions {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .actions h3 {
            margin-bottom: 1rem;
            color: #333;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .loading {
            text-align: center;
            padding: 2rem;
        }

        .spinner {
            border: 2px solid #f3f3f3;
            border-top: 2px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .error {
            background: #ffe6e6;
            color: #d63031;
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
            border-left: 4px solid #d63031;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <h1>Rimanaq</h1>
        </div>
        <div class="user-info">
            <span id="userName">Cargando...</span>
            <button class="btn btn-danger" onclick="logout()">Cerrar Sesión</button>
        </div>
    </div>

    <div class="container">
        <div id="loadingContainer" class="loading">
            <div class="spinner"></div>
            <p>Cargando dashboard...</p>
        </div>

        <div id="dashboardContent" style="display: none;">
            <div class="welcome">
                <h2>¡Bienvenido de vuelta!</h2>
                <p>Desde aquí puedes acceder a todos los cursos de lenguaje de señas y seguir tu progreso de aprendizaje.</p>
            </div>

            <div class="stats">
                <div class="stat-card">
                    <h3 id="totalCourses">-</h3>
                    <p>Cursos Disponibles</p>
                </div>
                <div class="stat-card">
                    <h3 id="totalLessons">-</h3>
                    <p>Lecciones Totales</p>
                </div>
                <div class="stat-card">
                    <h3 id="totalProgress">-</h3>
                    <p>Tu Progreso</p>
                </div>
            </div>

            <div class="actions">
                <h3>Acciones Rápidas</h3>
                <div class="action-buttons">
                    <button class="btn" onclick="viewCourses()">Ver Cursos</button>
                    <button class="btn" onclick="viewProgress()">Ver Mi Progreso</button>
                    <button class="btn" onclick="continueLearning()">Continuar Aprendiendo</button>
                </div>
            </div>
        </div>

        <div id="errorContainer" style="display: none;"></div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let authToken = localStorage.getItem('auth_token');
        let userData = JSON.parse(localStorage.getItem('user_data') || '{}');

        // Verificar autenticación al cargar
        window.addEventListener('load', () => {
            if (!authToken) {
                window.location.href = '/login';
                return;
            }
            
            initDashboard();
        });

        // Inicializar dashboard
        async function initDashboard() {
            try {
                // Mostrar información del usuario
                document.getElementById('userName').textContent = userData.name || 'Usuario';
                
                // Cargar estadísticas
                await loadStats();
                
                // Ocultar loading y mostrar contenido
                document.getElementById('loadingContainer').style.display = 'none';
                document.getElementById('dashboardContent').style.display = 'block';
                
            } catch (error) {
                console.error('Error inicializando dashboard:', error);
                showError('Error al cargar el dashboard');
            }
        }

        // Cargar estadísticas
        async function loadStats() {
            try {
                // Cargar cursos
                const coursesResponse = await fetchWithAuth('/api/courses');
                const courses = await coursesResponse.json();
                document.getElementById('totalCourses').textContent = courses.data ? courses.data.length : 0;

                // Cargar lecciones (de todos los cursos)
                let totalLessons = 0;
                if (courses.data) {
                    for (const course of courses.data) {
                        const lessonsResponse = await fetchWithAuth(`/api/courses/${course.id}/lessons`);
                        const lessons = await lessonsResponse.json();
                        totalLessons += lessons.data ? lessons.data.length : 0;
                    }
                }
                document.getElementById('totalLessons').textContent = totalLessons;

                // Cargar progreso del usuario
                const progressResponse = await fetchWithAuth(`/api/users/${userData.id}/progress`);
                const progress = await progressResponse.json();
                document.getElementById('totalProgress').textContent = progress.data ? progress.data.length : 0;

            } catch (error) {
                console.error('Error cargando estadísticas:', error);
                // Mostrar valores por defecto en caso de error
                document.getElementById('totalCourses').textContent = '0';
                document.getElementById('totalLessons').textContent = '0';
                document.getElementById('totalProgress').textContent = '0';
            }
        }

        // Función helper para hacer peticiones autenticadas
        async function fetchWithAuth(url, options = {}) {
            const defaultOptions = {
                headers: {
                    'Authorization': `Bearer ${authToken}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            };

            const mergedOptions = {
                ...defaultOptions,
                ...options,
                headers: {
                    ...defaultOptions.headers,
                    ...(options.headers || {})
                }
            };

            const response = await fetch(url, mergedOptions);
            
            if (response.status === 401) {
                // Token expirado, redirigir al login
                localStorage.removeItem('auth_token');
                localStorage.removeItem('user_data');
                window.location.href = '/login';
                return;
            }

            return response;
        }

        // Función para mostrar errores
        function showError(message) {
            const errorContainer = document.getElementById('errorContainer');
            errorContainer.innerHTML = `<div class="error">${message}</div>`;
            errorContainer.style.display = 'block';
            
            document.getElementById('loadingContainer').style.display = 'none';
            document.getElementById('dashboardContent').style.display = 'none';
        }

        // Cerrar sesión
        async function logout() {
            try {
                await fetchWithAuth('/api/logout', {
                    method: 'POST'
                });
            } catch (error) {
                console.error('Error cerrando sesión:', error);
            } finally {
                localStorage.removeItem('auth_token');
                localStorage.removeItem('user_data');
                window.location.href = '/login';
            }
        }

        // Funciones de acciones rápidas (por ahora solo muestran alertas)
        function viewCourses() {
            alert('Función "Ver Cursos" - Por implementar');
        }

        function viewProgress() {
            alert('Función "Ver Mi Progreso" - Por implementar');
        }

        function continueLearning() {
            alert('Función "Continuar Aprendiendo" - Por implementar');
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
</body>
</html>