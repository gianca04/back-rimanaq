@extends('layouts.app')

@section('title', 'Crear Curso')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Crear Nuevo Curso</h3>
        <a href="{{ route('web.courses.index') }}" class="btn btn-secondary">Volver a Cursos</a>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Información del Curso</h5>
        </div>
        <div class="card-body">
            @include('course.form')
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="module">
    import routes from '/js/routes.js';
    
    const courseForm = document.getElementById('courseForm');
    const token = localStorage.getItem('auth_token');

    courseForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = {
            name: courseForm.name.value,
            description: courseForm.description.value,
            image_path: courseForm.image_path.value,
            color: courseForm.color.value
        };

        try {
            const response = await fetch('/api/courses', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(formData)
            });

            if (response.ok) {
                window.location.href = routes.courses || '/dashboard/courses';
            } else {
                alert('Error al crear el curso');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al crear el curso');
        }
    });
</script>
@endsection