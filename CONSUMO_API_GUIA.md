# 📚 Consumo de API - Gestión de Cursos

## 🎯 Descripción General

Esta implementación muestra cómo consumir la API REST de cursos desde las vistas web de Laravel, siguiendo las mejores prácticas y convenciones de desarrollo moderno.

## 🏗️ Arquitectura Implementada

### 1. **Separación de Responsabilidades**
```
📁 Controllers/
├── 📁 API/V1/           # Controladores de API
│   └── CourseController.php
└── 📁 Web/              # Controladores de vistas web
    └── CourseWebController.php
```

### 2. **Estructura de Vistas**
```
📁 resources/views/course/
├── index_new.blade.php    # Lista de cursos mejorada
├── create_new.blade.php   # Formulario de creación
├── edit.blade.php         # Formulario de edición
└── show.blade.php         # Vista de detalles
```

### 3. **Utilidades JavaScript**
```
📁 public/js/
└── auth-helper.js         # Manejo de autenticación
```

## 🚀 Características Implementadas

### ✅ Vista Index (Lista de Cursos)
- **Consumo de API**: Carga asíncrona de cursos desde `/api/courses`
- **Interfaz Moderna**: Cards responsivas con gradientes y efectos hover
- **Estados de Carga**: Spinner, estado vacío, y manejo de errores
- **Acciones CRUD**: Ver, editar, eliminar con confirmación
- **Alertas**: Sistema de notificaciones integrado

### ✅ Vista Create (Crear Curso)
- **Formulario Validado**: Validación client-side y server-side
- **Vista Previa**: Color picker con preview en tiempo real
- **Estados UX**: Loading states, validación visual, contador de caracteres
- **Integración API**: POST a `/api/courses` con manejo de errores 422

### ✅ Sistema de Autenticación
- **Multi-source Token**: localStorage, sessionStorage, meta tags
- **JWT Validation**: Verificación de expiración automática
- **Error Handling**: Redirección automática en 401
- **Helper Global**: Utilidades reutilizables en todo el proyecto

## 📖 Guía de Uso

### 1. **Configuración Inicial**

Asegúrate de que las rutas estén correctamente configuradas:

```php
// routes/web.php
Route::prefix('dashboard')->name('web.')->group(function () {
    Route::get('/courses', [CourseWebController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [CourseWebController::class, 'create'])->name('courses.create');
    Route::get('/courses/{id}', [CourseWebController::class, 'show'])->name('courses.show');
    Route::get('/courses/{id}/edit', [CourseWebController::class, 'edit'])->name('courses.edit');
});
```

### 2. **Navegación**

```bash
# Lista de cursos
http://localhost:8000/dashboard/courses

# Crear nuevo curso
http://localhost:8000/dashboard/courses/create

# Editar curso
http://localhost:8000/dashboard/courses/{id}/edit
```

### 3. **Autenticación**

El sistema maneja automáticamente:
- Token de autenticación desde múltiples fuentes
- Verificación de expiración JWT
- Redirección automática al login si no está autenticado

```javascript
// Uso del AuthHelper
const token = AuthHelper.getToken();
const isAuth = AuthHelper.isAuthenticated();
const user = AuthHelper.getUserFromToken(token);
```

## 🔧 API Endpoints Consumidos

### Cursos
```http
GET    /api/courses           # Listar cursos
POST   /api/courses           # Crear curso
GET    /api/courses/{id}      # Ver curso
PUT    /api/courses/{id}      # Actualizar curso
DELETE /api/courses/{id}      # Eliminar curso
```

### Estructura de Respuesta
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Curso Básico LSP",
      "description": "Descripción del curso",
      "color": "#3498db",
      "image_path": "/images/curso.jpg",
      "created_at": "2025-10-05T...",
      "updated_at": "2025-10-05T..."
    }
  ]
}
```

## 🎨 Características de UX/UI

### 1. **Loading States**
- Spinners durante carga de datos
- Botones con estado de carga
- Feedback visual inmediato

### 2. **Validación de Formularios**
```javascript
// Validación en tiempo real
field.addEventListener('blur', () => this.validateField(field));
field.addEventListener('input', () => this.clearFieldError(field));
```

### 3. **Sistema de Alertas**
```javascript
// Mostrar notificaciones
this.showAlert('Curso creado exitosamente', 'success');
this.showAlert('Error al cargar datos', 'danger');
```

### 4. **Responsive Design**
```css
.course-card {
    transition: all 0.3s ease;
}
.course-card:hover {
    transform: translateY(-8px);
}
```

## 🛡️ Seguridad Implementada

### 1. **CSRF Protection**
```javascript
headers: {
    'X-CSRF-TOKEN': this.config.csrfToken,
    'Authorization': `Bearer ${token}`
}
```

### 2. **XSS Prevention**
```javascript
escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
```

### 3. **Token Validation**
```javascript
if (AuthHelper.isTokenExpired(token)) {
    AuthHelper.handleUnauthorized();
}
```

## 🔄 Flujo de Trabajo Típico

### 1. **Crear un Nuevo Curso**
```
Usuario → /dashboard/courses/create 
       → Llena formulario 
       → JavaScript valida 
       → POST /api/courses 
       → Redirección a lista
```

### 2. **Ver Lista de Cursos**
```
Usuario → /dashboard/courses 
       → JavaScript carga desde /api/courses 
       → Renderiza cards dinámicamente 
       → Acciones CRUD disponibles
```

### 3. **Eliminar Curso**
```
Usuario → Click eliminar 
       → Modal de confirmación 
       → DELETE /api/courses/{id} 
       → Actualiza vista local 
       → Muestra notificación
```

## 🎯 Mejores Prácticas Aplicadas

### 1. **Separación de Concerns**
- Controladores API separados de controladores web
- Lógica JavaScript organizada en clases
- Vistas enfocadas en presentación

### 2. **Error Handling Robusto**
```javascript
try {
    const response = await fetch(url, options);
    if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
    }
    // Procesar respuesta exitosa
} catch (error) {
    console.error('Error:', error);
    this.showAlert('Error message', 'danger');
}
```

### 3. **Progressive Enhancement**
- Funciona sin JavaScript (formularios básicos)
- JavaScript mejora la experiencia
- Fallbacks apropiados

### 4. **Código Autodocumentado**
```javascript
/**
 * Crea una tarjeta de curso con todas las funcionalidades
 * @param {Object} course - Datos del curso
 * @returns {HTMLElement} Elemento DOM de la tarjeta
 */
createCourseCard(course) {
    // Implementación clara y bien documentada
}
```

## 🚀 Próximos Pasos

1. **Implementar vistas de edición y detalles**
2. **Agregar funcionalidad de búsqueda y filtros**
3. **Implementar paginación para grandes datasets**
4. **Agregar funcionalidad de importación/exportación**
5. **Implementar notificaciones en tiempo real**

## 📝 Notas Importantes

- **Tokens de Autenticación**: Se manejan automáticamente desde múltiples fuentes
- **CSRF**: Protección habilitada en todas las peticiones
- **Responsive**: Diseño optimizado para móviles y desktop
- **Accesibilidad**: Labels, ARIA attributes, y navegación por teclado
- **Performance**: Carga lazy, estados optimizados, y manejo eficiente del DOM

---

*Esta implementación sigue las mejores prácticas de Laravel, JavaScript moderno, y principios de UX/UI para crear una experiencia de usuario excepcional.*