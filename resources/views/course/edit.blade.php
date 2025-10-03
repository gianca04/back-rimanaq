@extends('layouts.app')

@section('title', 'Editar Curso')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Editar Curso</h3>
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
    
    // Obtener ID del curso de la URL
    const courseId = window.location.pathname.split('/').pop();

    // Cargar datos del curso al cargar la página
    async function loadCourseData() {
        try {
            const response = await fetch(`/api/courses/${courseId}`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            
            const data = await response.json();
            if (data.success && data.data) {
                courseForm.name.value = data.data.name;
                courseForm.description.value = data.data.description;
                courseForm.image_path.value = data.data.image_path || '';
                courseForm.color.value = data.data.color || '';
            }
        } catch (error) {
            console.error('Error al cargar curso:', error);
        }
    }

    courseForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = {
            name: courseForm.name.value,
            description: courseForm.description.value,
            image_path: courseForm.image_path.value,
            color: courseForm.color.value
        };

        try {
            const response = await fetch(`/api/courses/${courseId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(formData)
            });

            if (response.ok) {
                window.location.href = routes.courses || '/dashboard/courses';
            } else {
                alert('Error al actualizar el curso');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al actualizar el curso');
        }
    });

    // Cargar datos al iniciar
    loadCourseData();
</script>
@endsection