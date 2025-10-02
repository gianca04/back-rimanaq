<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="/dashboard">
            <i class="fas fa-graduation-cap me-2"></i>
            Rimanaq Admin
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="/dashboard">
                        <i class="fas fa-tachometer-alt me-1"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('dashboard/courses*') ? 'active' : '' }}" href="/dashboard/courses">
                        <i class="fas fa-book me-1"></i>
                        Cursos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('dashboard/lessons*') ? 'active' : '' }}" href="/dashboard/lessons">
                        <i class="fas fa-list-alt me-1"></i>
                        Lecciones
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('dashboard/gestures*') ? 'active' : '' }}" href="/dashboard/gestures">
                        <i class="fas fa-hand-paper me-1"></i>
                        Gestos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('dashboard/progress*') ? 'active' : '' }}" href="/dashboard/progress">
                        <i class="fas fa-chart-line me-1"></i>
                        Progreso
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <button class="btn btn-outline-light" onclick="App.logout()">
                        <i class="fas fa-sign-out-alt me-1"></i>
                        Cerrar Sesión
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>