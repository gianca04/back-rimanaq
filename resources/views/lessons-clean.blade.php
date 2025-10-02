@extends('layouts.dashboard')

@section('title', 'Gestión de Lecciones')

@section('breadcrumbs')
    @include('components.breadcrumbs', ['breadcrumbs' => [
        ['title' => 'Dashboard', 'url' => '/dashboard', 'icon' => 'fas fa-tachometer-alt'],
        ['title' => 'Lecciones', 'icon' => 'fas fa-list-alt']
    ]])
@endsection

@section('page-header')
    <x-page-header 
        title="Gestión de Lecciones"
        subtitle="Administra las lecciones de todos los cursos desde un solo lugar"
        icon="fas fa-list-alt"
        :stats="['id' => 'totalLessons', 'value' => '0', 'label' => 'Lecciones totales']"
    />
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <x-data-table containerId="LessonsTableContainer" />
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/jtable-manager.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Cargar cursos y luego inicializar tabla
            loadCoursesAndInitTable();
        });

        async function loadCoursesAndInitTable() {
            try {
                App.Utils.showLoading();
                
                // Cargar jTable
                await JTableManager.loadJTable();
                
                // Cargar cursos
                const response = await App.API.request(App.Config.api.endpoints.courses);
                const coursesOptions = {};
                
                if (response.data && response.data.length > 0) {
                    response.data.forEach(function(course) {
                        coursesOptions[course.id] = course.name;
                    });
                } else {
                    App.Utils.showAlert('Advertencia: No se encontraron cursos. Crea al menos un curso primero.', 'warning');
                }
                
                // Inicializar tabla
                JTableManager.initialize('#LessonsTableContainer', 'lessons', {
                    title: 'Gestión de Lecciones',
                    coursesOptions: coursesOptions,
                    defaultSorting: 'course_id ASC, level_number ASC',
                    onRecordAdded: function(event, data) {
                        updateStats();
                    },
                    onRecordDeleted: function(event, data) {
                        updateStats();
                    }
                });
                
                updateStats();
                
            } catch (error) {
                console.error('Error initializing lessons page:', error);
                App.Utils.showAlert('Error al cargar la página: ' + error.message, 'danger');
            } finally {
                App.Utils.hideLoading();
            }
        }

        function updateStats() {
            // Actualizar estadísticas
            setTimeout(() => {
                const tableRows = $('#LessonsTableContainer .jtable-data-row').length;
                $('#totalLessons').text(tableRows);
            }, 500);
        }

        // Función global para compatibilidad
        window.viewLessonGestures = function(lessonId, lessonName) {
            const encodedName = encodeURIComponent(lessonName);
            window.location.href = `/dashboard/lessons/${lessonId}/gestures?lessonName=${encodedName}`;
        };
    </script>
@endpush