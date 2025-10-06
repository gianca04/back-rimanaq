{{-- resources/views/components/auth-check.blade.php --}}

{{-- Meta tag para CSRF token --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Meta tag para login URL --}}
<meta name="login-url" content="{{ route('web.login') }}">

{{-- Meta tag para token de API si está disponible en sesión --}}
@if(session('auth_token'))
    <meta name="api-token" content="{{ session('auth_token') }}">
@endif

{{-- Incluir rutas de JavaScript --}}
<script type="module">
    import routes from "{{ asset('js/routes.js') }}";
    window.routes = routes;
</script>

{{-- Incluir el helper de autenticación --}}
<script src="{{ asset('js/auth-helper.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Verificar autenticación solo en rutas protegidas
        const protectedRoutes = ['/dashboard'];
        const currentPath = window.location.pathname;
        
        const isProtectedRoute = protectedRoutes.some(route => 
            currentPath.startsWith(route)
        );
        
        if (isProtectedRoute) {
            const token = AuthHelper.getToken();
            console.log('🔍 Verificando autenticación en ruta protegida:', currentPath);
            console.log('📱 Token encontrado:', token ? 'SÍ (longitud: ' + token.length + ')' : 'NO');
            
            // Verificar si hay token (simplificado temporalmente)
            if (!token || token.length === 0) {
                console.log('❌ No hay token de autenticación. Redirigiendo al login...');
                AuthHelper.handleUnauthorized(true); // Redirección inmediata
                return;
            }
            
            console.log('✅ Usuario autenticado correctamente - token válido');
        }
        
        // Funciones globales para compatibilidad con código existente
        window.getAuthToken = () => AuthHelper.getToken();
        window.getUserData = () => {
            const token = AuthHelper.getToken();
            return token ? AuthHelper.getUserFromToken(token) : {};
        };
        
        // Función global para mostrar alertas
        window.showAlert = (message, type = 'info') => {
            // Buscar contenedor de alertas existente o crearlo
            let container = document.getElementById('alert-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'alert-container';
                container.className = 'position-fixed top-0 end-0 p-3';
                container.style.zIndex = '9999';
                document.body.appendChild(container);
            }
            
            const alertId = 'alert-' + Date.now();
            const alertHtml = `
                <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
                    <i class="fas fa-${getAlertIcon(type)} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', alertHtml);
            
            // Auto-remover después de 5 segundos
            setTimeout(() => {
                const alert = document.getElementById(alertId);
                if (alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 5000);
        };
        
        function getAlertIcon(type) {
            const icons = {
                'success': 'check-circle',
                'danger': 'exclamation-triangle',
                'warning': 'exclamation-circle',
                'info': 'info-circle'
            };
            return icons[type] || 'info-circle';
        }
    });
</script>