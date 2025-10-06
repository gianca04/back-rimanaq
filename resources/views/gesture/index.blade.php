@extends('layouts.app')

@section('title', 'Gestos')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Gestos</h3>
        <a class="btn btn-primary" href="{{ route('web.gestures.create') }}">Nuevo gesto</a>
    </div>

    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Lección</th>
                <th>Frames</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="gesturesTableBody">
            <!-- Gestos renderizados por JS -->
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<script type="module">
    const apiBase = '/api/gestures';
    const token = localStorage.getItem('auth_token');
    const tableBody = document.getElementById('gesturesTableBody');

    async function fetchGestures() {
        try {
            const res = await fetch(apiBase, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });
            const data = await res.json();

            tableBody.innerHTML = '';
            if (data.success && Array.isArray(data.data)) {
                data.data.forEach(gesture => {
                    const gestureName = gesture.gesture_data?.name || 'Sin nombre';
                    const frameCount = gesture.gesture_data?.frameCount || 0;
                    const lessonName = gesture.lesson?.name || 'Sin lección';

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${gesture.id}</td>
                        <td><strong>${gestureName}</strong></td>
                        <td>${lessonName}</td>
                        <td><span class="badge bg-info">${frameCount} frames</span></td>
                        <td>
                            <button class="btn btn-sm btn-secondary me-1" onclick="editGesture(${gesture.id})">Editar</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteGesture(${gesture.id})">Eliminar</button>
                        </td>
                    `;
                    tableBody.appendChild(tr);
                });
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    window.editGesture = function(id) {
        window.location.href = `/dashboard/gestures/edit/${id}`;
    }

    window.deleteGesture = async function(id) {
        if (!confirm('¿Eliminar este gesto?')) return;

        try {
            const res = await fetch(`${apiBase}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });

            if (res.ok) {
                alert('Gesto eliminado exitosamente');
                fetchGestures();
            } else {
                alert('Error al eliminar el gesto');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al eliminar el gesto');
        }
    }

    fetchGestures();
</script>
@endsection