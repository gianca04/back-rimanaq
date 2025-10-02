@extends('layouts.dashboard')

@section('title', 'Lecciones del Curso')

@section('breadcrumbs')
    @include('components.breadcrumbs', ['breadcrumbs' => [
        ['title' => 'Dashboard', 'url' => '/dashboard', 'icon' => 'fas fa-tachometer-alt'],
        ['title' => 'Cursos', 'url' => '/dashboard/courses', 'icon' => 'fas fa-book'],
        ['title' => 'Lecciones', 'icon' => 'fas fa-list-alt']
    ]])
@endsection

@section('content')
    <x-info-card>
        <h4 id="course-title">Cargando información del curso...</h4>
        <p class="mb-0">Gestiona las lecciones de este curso</p>
    </x-info-card>
    
    <div class="row">
        <div class="col-12">
            <x-data-table containerId="LessonsTableContainer" />
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/jtable-manager.js') }}"></script>
    <script>
        let courseId = null;
        let courseName = '';

        $(document).ready(function() {
            initializePage();
        });

        async function initializePage() {
            try {
                // Extraer courseId de la URL
                const pathParts = window.location.pathname.split('/');
                console.log('URL parts:', pathParts);
                
                if (pathParts.length >= 4 && pathParts[2] === 'courses') {
                    courseId = parseInt(pathParts[3]);
                } else {
                    throw new Error('ID del curso no encontrado en la URL');
                }
                
                if (!courseId || isNaN(courseId)) {
                    throw new Error('ID del curso no válido: ' + courseId);
                }
                
                // Obtener nombre del curso desde parámetros URL
                const urlParams = new URLSearchParams(window.location.search);
                courseName = urlParams.get('courseName') || 'Curso';
                
                // Actualizar título
                document.getElementById('course-title').textContent = 'Lecciones de: ' + courseName;
                
                console.log('Course ID:', courseId, 'Course Name:', courseName);
                
                // Cargar y configurar tabla
                await loadTableForCourse();
                
            } catch (error) {
                console.error('Error initializing course lessons page:', error);
                App.Utils.showAlert('Error: ' + error.message, 'danger');
                
                // Redirigir a cursos si hay error
                setTimeout(() => {
                    window.location.href = '/dashboard/courses';
                }, 3000);
            }
        }

        async function loadTableForCourse() {
            try {
                App.Utils.showLoading();
                
                // Cargar jTable
                await JTableManager.loadJTable();
                
                // Inicializar tabla específica para el curso
                JTableManager.initialize('#LessonsTableContainer', 'lessons', {
                    title: 'Lecciones del Curso: ' + courseName,
                    courseId: courseId,
                    defaultSorting: 'level_number ASC',
                    onRecordAdded: function(event, data) {
                        App.Utils.showAlert('Lección creada exitosamente', 'success');
                    },
                    onRecordUpdated: function(event, data) {
                        App.Utils.showAlert('Lección actualizada exitosamente', 'success');
                    },
                    onRecordDeleted: function(event, data) {
                        App.Utils.showAlert('Lección eliminada exitosamente', 'success');
                    }
                });
                
            } catch (error) {
                console.error('Error loading table:', error);
                App.Utils.showAlert('Error al cargar la tabla: ' + error.message, 'danger');
            } finally {
                App.Utils.hideLoading();
            }
        }

        // Función global para ver gestos
        window.viewLessonGestures = function(lessonId, lessonName) {
            const encodedName = encodeURIComponent(lessonName);
            const encodedCourseName = encodeURIComponent(courseName);
            const url = `/dashboard/lessons/${lessonId}/gestures?lessonName=${encodedName}&courseId=${courseId}&courseName=${encodedCourseName}`;
            console.log('Navigating to gestures:', url);
            window.location.href = url;
        };
    </script>
@endpush