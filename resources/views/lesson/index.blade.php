@extends('layouts.app')

@section('title', 'Lecciones')

@section('content')
<div class="py-4">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<h3 class="mb-0">Lecciones</h3>
		<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#lessonModal" id="newLessonBtn">Nueva lección</button>
	</div>

	<!-- Filtros -->
	<div class="row mb-3">
		<div class="col-md-4">
			<select id="courseFilter" class="form-select">
				<option value="">Todos los cursos</option>
				<!-- Los cursos se cargan dinámicamente -->
			</select>
		</div>
		<div class="col-md-3">
			<select id="difficultyFilter" class="form-select">
				<option value="">Todas las dificultades</option>
				<option value="fácil">Fácil</option>
				<option value="intermedio">Intermedio</option>
				<option value="difícil">Difícil</option>
			</select>
		</div>
	</div>

	<table class="table table-bordered table-hover">
		<thead class="table-light">
			<tr>
				<th>Curso</th>
				<th>Nivel</th>
				<th>Nombre</th>
				<th>Descripción</th>
				<th>Dificultad</th>
				<th>Duración</th>
				<th>Acciones</th>
			</tr>
		</thead>
		<tbody id="lessonsTableBody">
			<!-- Lecciones renderizadas por JS -->
		</tbody>
	</table>
</div>

<!-- Modal para crear/editar lección -->
<div class="modal fade" id="lessonModal" tabindex="-1" aria-labelledby="lessonModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="lessonModalLabel">Nueva lección</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
			</div>
			<div class="modal-body">
				@include('lesson.form')
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script type="module">
	// Rutas API
	const apiBase = '/api/lessons';
	const coursesApiBase = '/api/courses';
	const token = localStorage.getItem('auth_token');

	// Elementos
	const tableBody = document.getElementById('lessonsTableBody');
	const lessonForm = document.getElementById('lessonForm');
	const modalLabel = document.getElementById('lessonModalLabel');
	const courseSelect = document.getElementById('course_id');
	const courseFilter = document.getElementById('courseFilter');
	const difficultyFilter = document.getElementById('difficultyFilter');
	
	let editingId = null;
	let allLessons = [];
	let allCourses = [];

	// Cargar cursos
	async function loadCourses() {
		try {
			const res = await fetch(coursesApiBase, {
				headers: { 'Authorization': `Bearer ${token}` }
			});
			const data = await res.json();
			
			if (data.success && Array.isArray(data.data)) {
				allCourses = data.data;
				
				// Llenar select del formulario
				courseSelect.innerHTML = '<option value="">Seleccionar curso...</option>';
				// Llenar filtro
				courseFilter.innerHTML = '<option value="">Todos los cursos</option>';
				
				data.data.forEach(course => {
					const option1 = new Option(course.name, course.id);
					const option2 = new Option(course.name, course.id);
					courseSelect.appendChild(option1);
					courseFilter.appendChild(option2);
				});
			}
		} catch (error) {
			console.error('Error al cargar cursos:', error);
		}
	}

	// Renderizar lecciones
	async function fetchLessons() {
		try {
			const res = await fetch(apiBase, {
				headers: { 'Authorization': `Bearer ${token}` }
			});
			const data = await res.json();
			
			if (data.success && Array.isArray(data.data)) {
				allLessons = data.data;
				renderLessons();
			}
		} catch (error) {
			console.error('Error al cargar lecciones:', error);
		}
	}

	function renderLessons() {
		let lessons = [...allLessons];
		
		// Aplicar filtros
		const courseFilterValue = courseFilter.value;
		const difficultyFilterValue = difficultyFilter.value;
		
		if (courseFilterValue) {
			lessons = lessons.filter(lesson => lesson.course_id == courseFilterValue);
		}
		
		if (difficultyFilterValue) {
			lessons = lessons.filter(lesson => lesson.difficulty === difficultyFilterValue);
		}

		tableBody.innerHTML = '';
		
		lessons.forEach(lesson => {
			const course = allCourses.find(c => c.id === lesson.course_id);
			const courseName = course ? course.name : 'N/A';
			
			const difficultyClass = {
				'fácil': 'success',
				'intermedio': 'warning', 
				'difícil': 'danger'
			}[lesson.difficulty] || 'secondary';

			const tr = document.createElement('tr');
			tr.innerHTML = `
				<td>${courseName}</td>
				<td>
					<span class="badge bg-primary">Nivel ${lesson.level_number}</span>
				</td>
				<td>${lesson.name}</td>
				<td>${lesson.description.length > 100 ? lesson.description.substring(0, 100) + '...' : lesson.description}</td>
				<td>
					<span class="badge bg-${difficultyClass}">${lesson.difficulty_label}</span>
				</td>
				<td>${lesson.formatted_duration}</td>
				<td>
					<button class="btn btn-sm btn-secondary me-1" onclick="editLesson(${lesson.id})">Editar</button>
					<button class="btn btn-sm btn-danger" onclick="deleteLesson(${lesson.id})">Eliminar</button>
				</td>
			`;
			tableBody.appendChild(tr);
		});
	}

	window.editLesson = async function(id) {
		editingId = id;
		modalLabel.textContent = 'Editar lección';
		
		const result = await window.apiRequest(`${apiBase}/${id}`, {
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
			
			const modal = new bootstrap.Modal(document.getElementById('lessonModal'));
			modal.show();
		}
	}

	window.deleteLesson = async function(id) {
		if (!confirm('¿Eliminar esta lección?')) return;
		
		const result = await window.apiRequest(`${apiBase}/${id}`, {
			method: 'DELETE'
		}, {
			successMessage: 'Lección eliminada exitosamente',
			errorMessage: 'Error al eliminar la lección'
		});

		if (result.success) {
			fetchLessons();
		}
	}

	document.getElementById('newLessonBtn').addEventListener('click', () => {
		editingId = null;
		modalLabel.textContent = 'Nueva lección';
		lessonForm.reset();
	});

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
		
		let url = apiBase;
		let method = 'POST';
		let successMessage = 'Lección creada exitosamente';
		
		if (editingId) {
			url += `/${editingId}`;
			method = 'PUT';
			successMessage = 'Lección actualizada exitosamente';
		}

		const result = await window.apiRequest(url, {
			method,
			body: JSON.stringify(formData)
		}, {
			successMessage,
			errorMessage: editingId ? 'Error al actualizar la lección' : 'Error al crear la lección'
		});

		if (result.success) {
			const modal = bootstrap.Modal.getInstance(document.getElementById('lessonModal'));
			modal.hide();
			fetchLessons();
		}
	});

	// Event listeners para filtros
	courseFilter.addEventListener('change', renderLessons);
	difficultyFilter.addEventListener('change', renderLessons);

	// Inicializar
	loadCourses().then(() => fetchLessons());
</script>
@endsection
