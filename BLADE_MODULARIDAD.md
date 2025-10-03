# Guía de Modularidad Blade - Rimanaq

## Estructura de Componentes Implementada

### 1. Componente Head (`components/head.blade.php`)
- **Propósito**: Centralizar todos los elementos `<head>` del HTML
- **Contenido**: Meta tags, CSS, favicon, título dinámico
- **Uso**: `@include('components.head')` en layouts

### 2. Componente Navbar (`components/navbar.blade.php`)
- **Propósito**: Barra de navegación reutilizable
- **Funcionalidad**: Enlaces de navegación y botón de logout
- **Uso**: Incluido automáticamente en `layouts/app.blade.php`

### 3. Componente Logout Script (`components/logout-script.blade.php`)
- **Propósito**: Funcionalidad de cerrar sesión reutilizable
- **Evita**: Duplicación de código entre vistas
- **Uso**: `@include('components.logout-script')` donde se necesite

### 4. Componente Auth Check (`components/auth-check.blade.php`)
- **Propósito**: Verificación de autenticación global
- **Funciones**: Verificar token, funciones globales para datos de usuario
- **Uso**: Incluido automáticamente en el layout principal

## Mejoras Implementadas

### ✅ Modularidad Mejorada
- Separación de responsabilidades en componentes específicos
- Reutilización de código entre vistas
- Mantenimiento más sencillo

### ✅ Eliminación de Duplicación
- Código de logout centralizado
- Scripts de autenticación unificados
- Elementos `<head>` centralizados

### ✅ Consistencia
- Navbar incluido en todas las vistas que extienden `app.blade.php`
- Verificación de autenticación automática
- Estructura uniforme en todas las páginas

## Buenas Prácticas Aplicadas

1. **Separación de Componentes**: Cada funcionalidad en su propio archivo
2. **Naming Consistent**: Nombres descriptivos y consistentes
3. **Reutilización**: Componentes que se pueden usar en múltiples vistas
4. **Mantenibilidad**: Cambios centralizados afectan todas las vistas

## Estructura de Archivos

```
resources/views/
├── components/
│   ├── auth-check.blade.php    # Verificación global de autenticación
│   ├── head.blade.php          # Elementos <head> centralizados
│   ├── logout-script.blade.php # Script de logout reutilizable
│   └── navbar.blade.php        # Navegación principal
├── layouts/
│   └── app.blade.php           # Layout principal modular
├── course/
│   ├── create.blade.php        # Página crear curso (completa)
│   ├── edit.blade.php          # Página editar curso (completa)
│   ├── form.blade.php          # Formulario reutilizable
│   └── index.blade.php         # Lista de cursos
└── dashboard.blade.php         # Dashboard principal
```

## Uso de los Componentes

### En cualquier vista que extienda el layout:
```blade
@extends('layouts.app')

@section('title', 'Mi Página')

@section('content')
    <!-- Contenido específico -->
@endsection

@section('scripts')
    <!-- Scripts específicos de la página -->
    @include('components.logout-script') {{-- Si necesita logout --}}
@endsection
```

### Para agregar estilos específicos:
```blade
@section('head')
    <link rel="stylesheet" href="/css/mi-estilo-especifico.css">
@endsection
```

## Ventajas de esta Estructura

1. **Mantenimiento**: Cambios en un componente se reflejan en todas las vistas
2. **Escalabilidad**: Fácil agregar nuevos componentes o modificar existentes
3. **Consistencia**: Todas las páginas siguen la misma estructura
4. **Performance**: Evita duplicación innecesaria de código
5. **Debugging**: Más fácil localizar y corregir problemas específicos