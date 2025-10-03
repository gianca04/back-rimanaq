{{-- resources/views/components/auth-check.blade.php --}}
<script type="module">
    import routes from '/js/routes.js';
    
    // Verificar si hay token de autenticación
    const token = localStorage.getItem('auth_token');
    
    if (!token) {
        window.location.href = routes.login;
    }
    
    // Función global para obtener el token
    window.getAuthToken = () => localStorage.getItem('auth_token');
    
    // Función global para obtener datos del usuario
    window.getUserData = () => JSON.parse(localStorage.getItem('user_data') || '{}');
</script>