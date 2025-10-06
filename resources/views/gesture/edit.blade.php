@extends('layouts.app')

@section('title', 'Editar Gesto')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Editar Gesto</h3>
        <a href="{{ route('web.gestures.index') }}" class="btn btn-secondary">Volver</a>
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
    const gestureForm = document.getElementById('gestureForm');
    const lessonSelect = document.getElementById('lesson_id');
    const token = localStorage.getItem('auth_token');
    const gestureId = '{{ $id }}';

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

    async function loadGesture() {
        try {
            const res = await fetch(`/api/gestures/${gestureId}`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const data = await res.json();
            
            if (data.success) {
                const gesture = data.data;
                gestureForm.lesson_id.value = gesture.lesson_id;
                gestureForm.gesture_name.value = gesture.gesture_data?.name || '';
                gestureForm.frames.value = gesture.gesture_data?.frames ? JSON.stringify(gesture.gesture_data.frames, null, 2) : '';
                gestureForm.isSequential.value = gesture.gesture_data?.isSequential ? 'true' : 'false';
            }
        } catch (error) {
            console.error('Error:', error);
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
                name: gestureForm.gesture_name.value.toUpperCase(),
                frames: framesData,
                frameCount: Array.isArray(framesData) ? framesData.length : 0,
                isSequential: gestureForm.isSequential.value === 'true'
            }
        };

        try {
            const res = await fetch(`/api/gestures/${gestureId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(formData)
            });

            if (res.ok) {
                alert('Gesto actualizado exitosamente');
                window.location.href = '/dashboard/gestures';
            } else {
                const result = await res.json();
                alert('Error: ' + (result.message || 'Error al actualizar el gesto'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al actualizar el gesto');
        }
    });

    document.getElementById('gesture_name').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });

    loadLessons().then(loadGesture);
</script>
@endsection
