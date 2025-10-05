@extends('layouts.app')

@section('title', 'Editar Gesto')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Editar Gesto</h3>
        <a href="{{ route('web.gestures.index') }}" class="btn btn-secondary">Volver a gestos</a>
    </div>
    <div class="card">
        <div class="card-body">
            @include('gesture.form')
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="module">
const lessonsApiBase = '/api/lessons';
const gesturesApiBase = '/api/gestures';
const token = localStorage.getItem('auth_token');
const gestureForm = document.getElementById('gestureForm');
const lessonSelect = document.getElementById('lesson_id');

// Obtener ID del gesto desde la URL
const gestureId = window.location.pathname.split('/').pop();

// Cargar lecciones
async function loadLessons() {
    try {
        const res = await fetch(lessonsApiBase, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const data = await res.json();
        if (data.success && Array.isArray(data.data)) {
            lessonSelect.innerHTML = '<option value="">Seleccionar lección...</option>';
            data.data.forEach(lesson => {
                const option = new Option(lesson.name, lesson.id);
                lessonSelect.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error al cargar lecciones:', error);
    }
}

// Cargar datos del gesto
async function loadGesture() {
    try {
        const result = await window.apiRequest(`${gesturesApiBase}/${gestureId}`, {
            method: 'GET'
        }, {
            successMessage: '',
            errorMessage: 'Error al cargar los datos del gesto'
        });
        if (result.success && result.data.data) {
            const gesture = result.data.data;
            gestureForm.lesson_id.value = gesture.lesson_id;
            gestureForm.gesture_name.value = gesture.gesture_data?.name || '';
            gestureForm.frames.value = gesture.gesture_data?.frames ? JSON.stringify(gesture.gesture_data.frames, null, 2) : '';
            gestureForm.isSequential.value = gesture.gesture_data?.isSequential ? 'true' : 'false';
        }
    } catch (error) {
        console.error('Error al cargar gesto:', error);
    }
}

gestureForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    let framesData = null;
    try {
        framesData = JSON.parse(gestureForm.frames.value);
    } catch (error) {
        alert('Los frames deben ser un JSON válido');
        return;
    }
    const formData = {
        lesson_id: parseInt(gestureForm.lesson_id.value),
        gesture_data: {
            name: gestureForm.gesture_name.value,
            frames: framesData,
            frameCount: Array.isArray(framesData) ? framesData.length : 0,
            isSequential: gestureForm.isSequential.value === 'true'
        }
    };
    const result = await window.apiRequest(`${gesturesApiBase}/${gestureId}`, {
        method: 'PUT',
        body: JSON.stringify(formData)
    }, {
        successMessage: 'Gesto actualizado exitosamente',
        errorMessage: 'Error al actualizar el gesto'
    });
    if (result.success) {
        window.location.href = '/dashboard/gestures';
    }
});

loadLessons().then(loadGesture);
</script>
@endsection
