<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - Rimanaq</title>
</head>

<body>
    <div class="header">
        <div class="logo">
            <h1>Rimanaq</h1>
        </div>
        <div class="user-info">
            <span id="userName">Cargando...</span>
            <button onclick="logout()">Cerrar Sesión</button>
        </div>
    </div>

    <div class="container">
        <div id="loadingContainer">
            <div class="spinner"></div>
            <p>Cargando dashboard...</p>
        </div>

        <div id="dashboardContent" style="display: none;">
            <div class="welcome">
                <h2>¡Bienvenido de vuelta!</h2>
                <p>Desde aquí puedes acceder a todos los cursos de lenguaje de señas y seguir tu progreso de aprendizaje.
                </p>
            </div>

            <div class="stats">
                <div>
                    <h3 id="totalCourses">-</h3>
                    <p>Cursos Disponibles</p>
                </div>
                <div>
                    <h3 id="totalLessons">-</h3>
                    <p>Lecciones Totales</p>
                </div>
                <div>
                    <h3 id="totalProgress">-</h3>
                    <p>Tu Progreso</p>
                </div>
            </div>

            <div class="actions">
                <h3>Acciones Rápidas</h3>
                <div>
                    <button onclick="viewCourses()">Ver Cursos</button>
                    <button onclick="viewProgress()">Ver Mi Progreso</button>
                    <button onclick="continueLearning()">Continuar Aprendiendo</button>
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
            errorContainer.innerHTML = `<div>${message}</div>`;
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
</body>

</html>