{{-- resources/views/components/logout-script.blade.php --}}
<script type="module">
    import routes from '/js/routes.js';
    
    function setupLogoutButton(buttonId) {
        const token = localStorage.getItem('auth_token');
        const logoutBtn = document.getElementById(buttonId);
        
        if (logoutBtn) {
            logoutBtn.addEventListener('click', async () => {
                try {
                    await fetch(routes.apiLogout, {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    });
                } catch (e) {
                    console.error('Error durante logout:', e);
                }
                
                localStorage.removeItem('auth_token');
                localStorage.removeItem('user_data');
                window.location.href = routes.login;
            });
        }
    }
    
    // Configurar botones de logout
    setupLogoutButton('logoutBtn');
</script>