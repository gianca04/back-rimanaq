<!DOCTYPE html>
<html lang="es">
<head>
    @include('components.head')
</head>
<body class="bg-light">
    @include('components.navbar')
    <div class="container">
        @yield('content')
    </div>
    
    {{-- Scripts comunes --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    {{-- Sistema de notificaciones Toast --}}
    @include('components.toastify')
    
    {{-- Verificación de autenticación --}}
    @include('components.auth-check')
    
    {{-- Scripts específicos de cada página --}}
    @yield('scripts')
</body>
</html>
