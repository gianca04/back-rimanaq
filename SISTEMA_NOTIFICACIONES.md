# Sistema de Notificaciones Toast - Rimanaq

## 🎯 Descripción
Sistema simple de notificaciones usando **Toastify** para mostrar mensajes de éxito, error, advertencia e información tras operaciones de API.

## 🚀 Implementación

### Instalación Automática
El sistema ya está incluido en `layouts/app.blade.php` mediante el componente `@include('components.toastify')`.

### Funciones Disponibles

#### 1. `showToast(message, type)`
Muestra una notificación toast individual.

```javascript
// Tipos disponibles: 'success', 'error', 'warning', 'info'
showToast('Operación exitosa', 'success');
showToast('Error al procesar', 'error');
showToast('Advertencia importante', 'warning');
showToast('Información útil', 'info');
```

#### 2. `apiRequest(url, options, messages)`
Función principal para hacer requests con notificaciones automáticas.

```javascript
// Ejemplo básico
const result = await apiRequest('/api/courses', {
    method: 'POST',
    body: JSON.stringify(data)
}, {
    successMessage: 'Curso creado exitosamente',
    errorMessage: 'Error al crear el curso'
});

if (result.success) {
    // Operación exitosa
    console.log(result.data);
} else {
    // Error manejado automáticamente con toast
    console.error(result.error);
}
```

#### 3. `handleApiResponse(response, successMessage, errorMessage)`
Función para manejar respuestas de fetch manualmente.

```javascript
const response = await fetch('/api/endpoint');
const result = await handleApiResponse(
    response, 
    'Éxito!', 
    'Error!'
);
```

## 📋 Ejemplos de Uso

### Crear Registro
```javascript
const result = await apiRequest('/api/courses', {
    method: 'POST',
    body: JSON.stringify({
        name: 'Nuevo Curso',
        description: 'Descripción'
    })
}, {
    successMessage: 'Curso creado exitosamente',
    errorMessage: 'Error al crear el curso'
});

if (result.success) {
    // Actualizar UI, cerrar modal, etc.
    modal.hide();
    loadData();
}
```

### Actualizar Registro
```javascript
const result = await apiRequest(`/api/courses/${id}`, {
    method: 'PUT',
    body: JSON.stringify(formData)
}, {
    successMessage: 'Curso actualizado exitosamente',
    errorMessage: 'Error al actualizar el curso'
});
```

### Eliminar Registro
```javascript
if (confirm('¿Confirmar eliminación?')) {
    const result = await apiRequest(`/api/courses/${id}`, {
        method: 'DELETE'
    }, {
        successMessage: 'Curso eliminado exitosamente',
        errorMessage: 'Error al eliminar el curso'
    });

    if (result.success) {
        loadData(); // Recargar lista
    }
}
```

### Obtener Datos (sin toast de éxito)
```javascript
const result = await apiRequest(`/api/courses/${id}`, {
    method: 'GET'
}, {
    successMessage: '', // Vacío = no mostrar toast de éxito
    errorMessage: 'Error al cargar los datos'
});

if (result.success) {
    populateForm(result.data.data);
}
```

### Toast Manual
```javascript
// Para casos especiales donde necesites control total
try {
    const response = await fetch('/api/custom-endpoint');
    const data = await response.json();
    
    if (response.ok) {
        showToast('Procesamiento completado', 'success');
    } else {
        showToast(data.message || 'Error', 'error');
    }
} catch (error) {
    showToast('Error de conexión', 'error');
}
```

## 🎨 Personalización

### Colores de Toast
Los colores están definidos en `components/toastify.blade.php`:

```javascript
const colors = {
    success: 'linear-gradient(to right, #00b09b, #96c93d)',
    error: 'linear-gradient(to right, #ff5f6d, #ffc371)',
    warning: 'linear-gradient(to right, #f093fb, #f5576c)',
    info: 'linear-gradient(to right, #4facfe, #00f2fe)'
};
```

### Configuración de Toastify
```javascript
Toastify({
    text: message,
    duration: 3000,        // 3 segundos
    close: true,           // Botón cerrar
    gravity: "top",        // Posición vertical
    position: "right",     // Posición horizontal
    backgroundColor: colors[type],
    stopOnFocus: true      // Pausar en hover
});
```

## 🔧 Integración en Nuevas Vistas

### 1. Vista que extiende el layout (automático)
```blade
@extends('layouts.app')
{{-- El sistema ya está disponible --}}
```

### 2. En el JavaScript de la vista
```blade
@section('scripts')
<script>
    // Las funciones globales ya están disponibles
    document.getElementById('saveBtn').addEventListener('click', async () => {
        const result = await apiRequest('/api/endpoint', {
            method: 'POST',
            body: JSON.stringify(data)
        }, {
            successMessage: 'Guardado exitosamente',
            errorMessage: 'Error al guardar'
        });
        
        if (result.success) {
            // Lógica de éxito
        }
    });
</script>
@endsection
```

## ✅ Ventajas

1. **Simplicidad**: Una sola función para manejar API + notificaciones
2. **Consistencia**: Todas las notificaciones tienen el mismo estilo
3. **Automático**: Manejo automático de errores y éxitos
4. **Ligero**: Toastify es una librería muy liviana
5. **Personalizable**: Fácil modificar colores y comportamiento
6. **Global**: Disponible en todas las vistas automáticamente

## 🐛 Manejo de Errores

El sistema maneja automáticamente:
- ❌ Errores de red
- ❌ Respuestas HTTP de error (4xx, 5xx)
- ❌ JSON malformado
- ❌ Errores de la API (`success: false`)
- ✅ Respuestas exitosas con notificación automática

## 📱 Responsive
Las notificaciones son completamente responsive y se adaptan a dispositivos móviles automáticamente.