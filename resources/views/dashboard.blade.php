@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="py-5">
    <div class="card shadow-sm p-4 mx-auto" style="max-width: 400px;">
        <h2 class="mb-3 text-center">Bienvenido, <span id="userName">Usuario</span></h2>
        <p class="text-center mb-4">Has iniciado sesión correctamente.</p>
        <div class="mb-3">
            <strong>Email:</strong> <span id="userEmail"></span>
        </div>
        <button id="logoutBtn" class="btn btn-danger w-100">Cerrar sesión</button>
    </div>
</div>
@endsection

@section('scripts')
<script type="module">
    // Obtener datos del usuario usando la función global
    const user = window.getUserData();

    // Mostrar datos en la vista
    document.getElementById('userName').textContent = user?.name || 'Usuario';
    document.getElementById('userEmail').textContent = user?.email || '';
</script>

@include('components.logout-script')
@endsection