@extends('layouts.app')

@section('title', 'Crear gesto')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Crear Nuevo Gesto</h3>
        <a href="{{ route('web.gestures.index') }}" class="btn btn-secondary">Volver a Gestos</a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Información del Curso</h5>
        </div>
        <div class="card-body">
            @include('gesture.form')
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="module">
    const gestureForm = document.getElementById('gestureForm');
    const lessonSelect = document.getElementById('lesson_id');
    const token = localStorage.getItem('auth_token');

    async function loadLessons() {
        try {
            const res = await fetch('/api/lessons', {
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
            console.error('Error:', error);
        }
    }

    gestureForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const jsonData = JSON.parse(gestureForm.frames.value);
        
        const formData = {
            lesson_id: parseInt(gestureForm.lesson_id.value),
            gesture_data: {
                name: gestureForm.gesture_name.value.toUpperCase(),
                frames: jsonData.gesture.frames,
                frameCount: jsonData.gesture.frameCount,
                isSequential: jsonData.gesture.isSequential
            }
        };

        const res = await fetch('/api/gestures', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify(formData)
        });

        const result = await res.json();

        if (res.ok && result.success) {
            alert('Gesto creado exitosamente');
            window.location.href = '/dashboard/gestures';
        } else {
            alert('Error: ' + (result.message || 'Error al crear el gesto'));
        }
    });

    document.getElementById('gesture_name').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });

    loadLessons();
</script>
@endsection