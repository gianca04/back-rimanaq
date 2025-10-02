<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Rimanaq</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo h1 {
            color: #333;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .logo p {
            color: #666;
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: bold;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn {
            width: 100%;
            padding: 0.75rem;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background: #5a6fd8;
        }

        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .error {
            background: #ffe6e6;
            color: #d63031;
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            border-left: 4px solid #d63031;
        }

        .success {
            background: #e6ffe6;
            color: #00b894;
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            border-left: 4px solid #00b894;
        }

        .loading {
            text-align: center;
            margin-top: 1rem;
        }

        .spinner {
            border: 2px solid #f3f3f3;
            border-top: 2px solid #667eea;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>Rimanaq</h1>
            <p>Iniciar Sesión</p>
        </div>

        <div id="message-container"></div>

        <form id="loginForm">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn" id="loginBtn">
                Iniciar Sesión
            </button>
        </form>

        <div id="loading" class="loading" style="display: none;">
            <div class="spinner"></div>
            <p>Iniciando sesión...</p>
        </div>
    </div>

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