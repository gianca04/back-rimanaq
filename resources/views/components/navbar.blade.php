{{-- resources/views/components/navbar.blade.php --}}
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('web.dashboard') }}">Rimanaq</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="/dashboard">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('web.courses.index') }}">Cursos</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="lessonsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Lecciones
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="lessonsDropdown">
                        <li><a class="dropdown-item" href="{{ route('web.lessons.index') }}">
                            <i class="bi bi-list-ul"></i> Gestionar Lecciones
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('web.lessons.create') }}">
                            <i class="bi bi-plus-circle"></i> Nueva Lección
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('web.lessons.content-demo') }}">
                            <i class="bi bi-gear"></i> Demo CRUD Contenido
                        </a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('web.gestures.index') }}">Gestos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/dashboard/progress">Progreso</a>
                </li>
            </ul>
            <button id="logoutBtn" class="btn btn-outline-danger ms-auto">Cerrar sesión</button>
        </div>
    </div>
</nav>

@include('components.logout-script')
