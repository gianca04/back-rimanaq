@extends('layouts.app')

@section('title', 'Cursos')

@section('content')
<div class="py-4">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<h3 class="mb-0">Cursos</h3>
		<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#courseModal" id="newCourseBtn">Nuevo curso</button>
	</div>
	<table class="table table-bordered table-hover">
		<thead class="table-light">
			<tr>
				<th>Nombre</th>
				<th>Descripción</th>
				<th>Color</th>
				<th>Acciones</th>
			</tr>
		</thead>
		<tbody id="coursesTableBody">
			<!-- Cursos renderizados por JS -->
		</tbody>
	</table>
</div>

<!-- Modal para crear/editar curso -->
<div class="modal fade" id="courseModal" tabindex="-1" aria-labelledby="courseModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="courseModalLabel">Nuevo curso</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
			</div>
			<div class="modal-body">
				@include('course.form')
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script type="module">
	// Rutas API
	const apiBase = '/api/courses';
	const token = localStorage.getItem('auth_token');

	// Elementos
	const tableBody = document.getElementById('coursesTableBody');
	const courseForm = document.getElementById('courseForm');
	const modalLabel = document.getElementById('courseModalLabel');
	let editingId = null;

	// Renderizar cursos
	async function fetchCourses() {
		const res = await fetch(apiBase, {
			headers: {
				'Authorization': `Bearer ${token}`
			}
		});
		const data = await res.json();
		tableBody.innerHTML = '';
		if (data.success && Array.isArray(data.data)) {
			data.data.forEach(course => {
				const tr = document.createElement('tr');
				tr.innerHTML = `
					<td>${course.name}</td>
					<td>${course.description}</td>
					<td><span style="background:${course.color};padding:2px 10px;border-radius:4px;color:#fff;">${course.color || ''}</span></td>
					<td>
						<button class="btn btn-sm btn-secondary me-1" onclick="editCourse(${course.id})">Editar</button>
						<button class="btn btn-sm btn-danger" onclick="deleteCourse(${course.id})">Eliminar</button>
					</td>
				`;
				tableBody.appendChild(tr);
			});
		}
	}

	window.editCourse = async function(id) {
		editingId = id;
		modalLabel.textContent = 'Editar curso';

		// Obtener datos del curso
		const result = await window.apiRequest(`${apiBase}/${id}`, {
			method: 'GET'
		}, {
			successMessage: '', // No mostrar mensaje para GET
			errorMessage: 'Error al cargar los datos del curso'
		});

		if (result.success && result.data.data) {
			const course = result.data.data;
			courseForm.name.value = course.name;
			courseForm.description.value = course.description;
			courseForm.image_path.value = course.image_path || '';
			courseForm.color.value = course.color || '';

			const modal = new bootstrap.Modal(document.getElementById('courseModal'));
			modal.show();
		}
	}

	window.deleteCourse = async function(id) {
		if (!confirm('¿Eliminar este curso?')) return;

		const result = await window.apiRequest(`${apiBase}/${id}`, {
			method: 'DELETE'
		}, {
			successMessage: 'Curso eliminado exitosamente',
			errorMessage: 'Error al eliminar el curso'
		});

		if (result.success) {
			fetchCourses();
		}
	}

	document.getElementById('newCourseBtn').addEventListener('click', () => {
		editingId = null;
		modalLabel.textContent = 'Nuevo curso';
		courseForm.reset();
	});

	courseForm.addEventListener('submit', async function(e) {
		e.preventDefault();
		const formData = {
			name: courseForm.name.value,
			description: courseForm.description.value,
			image_path: courseForm.image_path.value,
			color: courseForm.color.value
		};

		let url = apiBase;
		let method = 'POST';
		let successMessage = 'Curso creado exitosamente';

		if (editingId) {
			url += `/${editingId}`;
			method = 'PUT';
			successMessage = 'Curso actualizado exitosamente';
		}

		const result = await window.apiRequest(url, {
			method,
			body: JSON.stringify(formData)
		}, {
			successMessage,
			errorMessage: editingId ? 'Error al actualizar el curso' : 'Error al crear el curso'
		});

		if (result.success) {
			const modal = bootstrap.Modal.getInstance(document.getElementById('courseModal'));
			modal.hide();
			fetchCourses();
		}
	});

	fetchCourses();
</script>
@endsection