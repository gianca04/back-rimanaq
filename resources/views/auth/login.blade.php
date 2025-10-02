<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />


    <title>Login - Rimanaq</title>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-sm">
        <h1 class="text-2xl font-bold text-center mb-4">Iniciar Sesión</h1>

        <div id="message-container"></div>

        <form id="loginForm" class="space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
                <input type="email" id="email" name="email" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Contraseña:</label>
                <input type="password" id="password" name="password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>

            <button type="submit" id="loginBtn" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Iniciar Sesión</button>
        </form>

        <div id="loading" class="text-center mt-4 hidden">
            <div role="status">
                <svg class="inline w-6 h-6 text-blue-600 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
            </div>
            <p class="text-sm text-gray-500 mt-2">Iniciando sesión...</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script>
        
        // Configurar CSRF token para todas las peticiones Ajax
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        const loginForm = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');
        const loading = document.getElementById('loading');
        const messageContainer = document.getElementById('message-container');

        // Función para mostrar mensajes
        function showMessage(message, type = 'error') {
            const messageDiv = document.createElement('div');
            messageDiv.className = type;
            messageDiv.textContent = message;
            messageContainer.innerHTML = '';
            messageContainer.appendChild(messageDiv);
        }

        // Función para limpiar mensajes
        function clearMessage() {
            messageContainer.innerHTML = '';
        }

        // Función para deshabilitar/habilitar el formulario
        function toggleForm(disabled) {
            const inputs = loginForm.querySelectorAll('input, button');
            inputs.forEach(input => {
                input.disabled = disabled;
            });
            
            if (disabled) {
                loading.style.display = 'block';
                loginBtn.style.display = 'none';
            } else {
                loading.style.display = 'none';
                loginBtn.style.display = 'block';
            }
        }

        // Manejar el envío del formulario
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            clearMessage();
            toggleForm(true);

            // Obtener valores de los campos - método más directo
            const email = document.querySelector('#email').value || '';
            const password = document.querySelector('#password').value || '';
            
            const data = {
                email: email.trim(),
                password: password.trim()
            };
            
            // Validar que los campos no estén vacíos
            if (!data.email || data.email === '') {
                showMessage('Por favor, ingresa tu email.');
                toggleForm(false);
                return;
            }
            
            if (!data.password || data.password === '') {
                showMessage('Por favor, ingresa tu contraseña.');
                toggleForm(false);
                return;
            }

            try {
                const response = await fetch('/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok) {
                    // Login exitoso
                    showMessage(result.message || 'Login exitoso', 'success');
                    
                    // Guardar token en localStorage
                    localStorage.setItem('auth_token', result.token);
                    localStorage.setItem('user_data', JSON.stringify(result.user));
                    
                    // Redirigir al dashboard
                    setTimeout(() => {
                        window.location.href = '/dashboard';
                    }, 1000);
                } else {
                    // Error en login
                    let errorMessage = 'Error en el login';
                    
                    if (result.errors) {
                        // Errores de validación - usar solo el primer error
                        const errors = Object.values(result.errors).flat();
                        errorMessage = errors[0] || errorMessage;
                    } else if (result.message) {
                        errorMessage = result.message;
                    }
                    
                    showMessage(errorMessage);
                    toggleForm(false);
                }
            } catch (error) {
                showMessage('Error de conexión. Por favor, inténtalo de nuevo.');
                toggleForm(false);
            }
        });

        // Verificar si ya hay un token guardado al cargar la página
        window.addEventListener('load', () => {
            const token = localStorage.getItem('auth_token');
            if (token) {
                // Ya hay una sesión, redirigir al dashboard
                window.location.href = '/dashboard';
            }
        });
    </script>
</body>
</html>