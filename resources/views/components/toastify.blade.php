{{-- resources/views/components/toastify.blade.php --}}
{{-- Toastify CSS --}}
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

{{-- Toastify JS --}}
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<script>
    // Función global para mostrar notificaciones toast
    window.showToast = function(message, type = 'success') {
        const colors = {
            success: 'linear-gradient(to right, #00b09b, #96c93d)',
            error: 'linear-gradient(to right, #ff5f6d, #ffc371)',
            warning: 'linear-gradient(to right, #f093fb, #f5576c)',
            info: 'linear-gradient(to right, #4facfe, #00f2fe)'
        };

        Toastify({
            text: message,
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            backgroundColor: colors[type] || colors.success,
            stopOnFocus: true
        }).showToast();
    };

    // Función para manejar respuestas de API automáticamente
    window.handleApiResponse = async function(response, successMessage = 'Operación exitosa', errorMessage = 'Error en la operación') {
        try {
            const data = await response.json();
            
            if (response.ok && (data.success !== false)) {
                if (successMessage) {
                    window.showToast(successMessage, 'success');
                }
                return { success: true, data: data };
            } else {
                const message = data.message || errorMessage;
                window.showToast(message, 'error');
                return { success: false, error: message, data: data };
            }
        } catch (error) {
            window.showToast(errorMessage, 'error');
            return { success: false, error: error.message };
        }
    };

    // Función simplificada para hacer requests con notificaciones automáticas
    window.apiRequest = async function(url, options = {}, messages = {}) {
        const {
            successMessage = 'Operación exitosa',
            errorMessage = 'Error en la operación'
        } = messages;

        try {
            const response = await fetch(url, {
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${window.getAuthToken()}`,
                    ...options.headers
                },
                ...options
            });

            const result = await window.handleApiResponse(response, successMessage, errorMessage);
            
            // No mostrar toast de éxito para operaciones GET o si successMessage está vacío
            if (result.success && (options.method === 'GET' || successMessage === '')) {
                // Solo retornar el resultado sin toast
                return result;
            }
            
            return result;
        } catch (error) {
            window.showToast(errorMessage, 'error');
            return { success: false, error: error.message };
        }
    };
</script>