@extends('layouts.app')

@section('title', 'Crear Lección')

@section('content')
<div class="py-4">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<h3 class="mb-0">Crear Nueva Lección</h3>
		<a href="{{ route('web.lessons.index') }}" class="btn btn-secondary">Volver a lecciones</a>
	</div>

	<div class="card">
		<div class="card-body">
			@include('lesson.form')
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script type="module">
	const coursesApiBase = '/api/courses';
	const lessonsApiBase = '/api/lessons';
	const token = localStorage.getItem('auth_token');
	
	const lessonForm = document.getElementById('lessonForm');
	const courseSelect = document.getElementById('course_id');

	// Cargar cursos
	async function loadCourses() {
		try {
			const res = await fetch(coursesApiBase, {
				headers: { 'Authorization': `Bearer ${token}` }
			});
			const data = await res.json();
			
			if (data.success && Array.isArray(data.data)) {
				courseSelect.innerHTML = '<option value="">Seleccionar curso...</option>';
				data.data.forEach(course => {
					const option = new Option(course.name, course.id);
					courseSelect.appendChild(option);
				});
			}
		} catch (error) {
			console.error('Error al cargar cursos:', error);
		}
	}

	lessonForm.addEventListener('submit', async function(e) {
		e.preventDefault();
		
		const formData = {
			course_id: parseInt(lessonForm.course_id.value),
			name: lessonForm.name.value,
			level_number: parseInt(lessonForm.level_number.value),
			description: lessonForm.description.value,
			difficulty: lessonForm.difficulty.value,
			time_minutes: parseInt(lessonForm.time_minutes.value),
			content: lessonForm.content.value // Enviar el contenido tal como está
		};

		const result = await window.apiRequest(lessonsApiBase, {
			method: 'POST',
			body: JSON.stringify(formData)
		}, {
			successMessage: 'Lección creada exitosamente',
			errorMessage: 'Error al crear la lección'
		});

		if (result.success) {
			// Redirigir a la lista de lecciones
			window.location.href = '/dashboard/lessons';
		}
	});

	// Cargar cursos al inicializar
	loadCourses();
</script>
@endsection
