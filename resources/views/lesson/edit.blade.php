@extends('layouts.app')

@section('title', 'Editar Lección')

@section('content')
<div class="py-4">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<h3 class="mb-0">Editar Lección</h3>
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
	
	// Obtener ID de la lección desde la URL
	const lessonId = window.location.pathname.split('/').pop();

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

	// Cargar datos de la lección
	async function loadLesson() {
		try {
			const result = await window.apiRequest(`${lessonsApiBase}/${lessonId}`, {
				method: 'GET'
			}, {
				successMessage: '',
				errorMessage: 'Error al cargar los datos de la lección'
			});

			if (result.success && result.data.data) {
				const lesson = result.data.data;
				lessonForm.course_id.value = lesson.course_id;
				lessonForm.name.value = lesson.name;
				lessonForm.level_number.value = lesson.level_number;
				lessonForm.description.value = lesson.description;
				lessonForm.difficulty.value = lesson.difficulty;
				lessonForm.time_minutes.value = lesson.time_minutes;
				lessonForm.content.value = lesson.content ? JSON.stringify(lesson.content, null, 2) : '';
			}
		} catch (error) {
			console.error('Error al cargar lección:', error);
		}
	}

	lessonForm.addEventListener('submit', async function(e) {
		e.preventDefault();
		
		// Validar JSON del contenido si no está vacío
		let contentData = null;
		if (lessonForm.content.value.trim()) {
			try {
				contentData = JSON.parse(lessonForm.content.value);
			} catch (error) {
				alert('El contenido debe ser un JSON válido');
				return;
			}
		}
		
		const formData = {
			course_id: parseInt(lessonForm.course_id.value),
			name: lessonForm.name.value,
			level_number: parseInt(lessonForm.level_number.value),
			description: lessonForm.description.value,
			difficulty: lessonForm.difficulty.value,
			time_minutes: parseInt(lessonForm.time_minutes.value),
			content: contentData
		};

		const result = await window.apiRequest(`${lessonsApiBase}/${lessonId}`, {
			method: 'PUT',
			body: JSON.stringify(formData)
		}, {
			successMessage: 'Lección actualizada exitosamente',
			errorMessage: 'Error al actualizar la lección'
		});

		if (result.success) {
			// Redirigir a la lista de lecciones
			window.location.href = '/dashboard/lessons';
		}
	});

	// Inicializar
	loadCourses().then(() => loadLesson());
</script>
@endsection
