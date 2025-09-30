<template>
  <div class="courses-list bg-gray-50 min-h-screen py-8">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-2xl font-semibold text-gray-800">Cursos</h1>
      <p class="mt-1 text-gray-500 text-base">Gestiona tus cursos de manera fácil y eficiente</p>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex flex-col justify-center items-center py-16">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      <span class="mt-4 text-gray-500 text-base">Cargando cursos...</span>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded p-4 mb-8 flex items-center gap-3">
      <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
      </svg>
      <div>
        <h3 class="text-base font-medium text-red-800">Error al cargar los cursos</h3>
        <p class="text-sm text-red-700 mt-1">{{ error }}</p>
      </div>
    </div>

    <!-- Empty State -->
  <div v-else-if="courses.length === 0" class="text-center py-16">
      <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
        <path
          d="M34 40h10v-4a6 6 0 00-10.712-3.714M34 40H14m20 0v-4a9.971 9.971 0 00-.712-3.714M14 40H4v-4a6 6 0 0110.713-3.714M14 40v-4c0-1.313.253-2.566.713-3.714m0 0A10.003 10.003 0 0124 26c4.21 0 7.813 2.602 9.288 6.286M30 14a6 6 0 11-12 0 6 6 0 0112 0zm12 6a4 4 0 11-8 0 4 4 0 018 0zm-28 0a4 4 0 11-8 0 4 4 0 018 0z"
          stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
      </svg>
  <h3 class="mt-4 text-lg font-semibold text-gray-800">No hay cursos disponibles</h3>
  <p class="mt-2 text-gray-500">Comienza creando tu primer curso.</p>
      <div class="mt-6">
        <router-link to="/courses/create"
          class="inline-flex items-center px-4 py-2 border border-blue-600 text-blue-600 text-sm font-medium rounded hover:bg-blue-50 transition">
          <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          Crear Nuevo Curso
        </router-link>
      </div>
    </div>

    <!-- Courses Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <div v-for="course in courses" :key="course.id"
        class="bg-white border border-gray-200 shadow-sm rounded-lg hover:shadow transition-shadow duration-200">
        <div class="px-8 py-8">
          <!-- Course Image or Color Header -->
          <div class="w-full h-32 rounded-lg mb-4 flex items-center justify-center overflow-hidden relative"
            :style="{ backgroundColor: course.color || '#6B7280' }">
            <img v-if="course.image_path" :src="course.image_path" :alt="course.name" class="w-full h-full object-cover rounded-lg"
              @error="handleImageError($event)" />
            <div v-else class="text-white text-center w-full h-full flex flex-col items-center justify-center">
              <svg class="h-10 w-10 mb-2 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
              <span class="text-sm font-medium text-white/90">{{ course.name }}</span>
            </div>
            <!-- Color badge -->
            <span class="absolute top-2 right-2 flex items-center gap-1 bg-white/80 text-gray-700 text-xs font-medium px-2 py-1 rounded shadow border border-gray-200">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" :fill="course.color || '#6B7280'" stroke="currentColor" stroke-width="2"/></svg>
              {{ course.color || '#6B7280' }}
            </span>
          </div>

          <!-- Course Info -->
          <h3 class="text-base font-semibold text-gray-800 mb-2">{{ course.name }}</h3>
          <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ course.description }}</p>

          <!-- Actions -->
          <div class="flex justify-between items-center">
            <span class="text-xs text-gray-500">Creado: {{ formatDate(course.created_at) }}</span>
            <div class="flex gap-2">
              <router-link :to="`/courses/${course.id}`"
                class="inline-flex items-center px-3 py-1 border border-blue-600 text-blue-600 text-xs font-medium rounded hover:bg-blue-50 transition">
                Ver Curso
              </router-link>
              <router-link :to="`/courses/${course.id}/edit`"
                class="inline-flex items-center px-3 py-1 border border-gray-300 text-gray-700 text-xs font-medium rounded hover:bg-gray-50 transition">
                Editar
              </router-link>
              <button @click="deleteCourse(course)"
                class="inline-flex items-center px-3 py-1 border border-red-600 text-red-600 text-xs font-medium rounded hover:bg-red-50 transition">
                Eliminar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-700 bg-opacity-40 flex items-center justify-center z-50">
      <div class="w-full max-w-sm mx-auto bg-white border border-gray-200 rounded-lg shadow-lg p-6">
        <div class="flex flex-col items-center">
          <div class="flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-2">
            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-gray-800 mb-2">Confirmar eliminación</h3>
          <p class="text-sm text-gray-500 mb-4 text-center">¿Estás seguro de que quieres eliminar el curso "{{ courseToDelete?.name }}"? Esta acción no se puede deshacer.</p>
          <div class="flex gap-2 w-full justify-center">
            <button @click="confirmDelete" :disabled="deleting"
              class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded hover:bg-red-700 disabled:opacity-50 transition w-24">
              {{ deleting ? 'Eliminando...' : 'Eliminar' }}
            </button>
            <button @click="cancelDelete"
              class="px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded hover:bg-gray-300 transition w-24">
              Cancelar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useToast } from '../composables/useToast.js';

// Estado reactivo
const courses = ref([]);
const loading = ref(true);
const error = ref(null);
const showDeleteModal = ref(false);
const courseToDelete = ref(null);
const deleting = ref(false);

// Toast notifications
const { success, error: showError } = useToast();

// Métodos
const fetchCourses = async () => {
  try {
    loading.value = true;
    error.value = null;

    const response = await axios.get('/api/courses');

    if (response.data.success) {
      courses.value = response.data.data;
    } else {
      error.value = response.data.message || 'Error al cargar los cursos';
    }
  } catch (err) {
    console.error('Error fetching courses:', err);
    error.value = err.response?.data?.message || 'Error de conexión al servidor';
  } finally {
    loading.value = false;
  }
};

const deleteCourse = (course) => {
  courseToDelete.value = course;
  showDeleteModal.value = true;
};

const confirmDelete = async () => {
  if (!courseToDelete.value) return;

  try {
    deleting.value = true;

    const response = await axios.delete(`/api/courses/${courseToDelete.value.id}`);

    if (response.data.success) {
      // Remove from local array
      courses.value = courses.value.filter(course => course.id !== courseToDelete.value.id);
      showDeleteModal.value = false;
      courseToDelete.value = null;

      // Show success message
      success('Curso eliminado exitosamente');
    } else {
      throw new Error(response.data.message || 'Error al eliminar el curso');
    }
  } catch (err) {
    console.error('Error deleting course:', err);
    showError(err.response?.data?.message || 'Error al eliminar el curso');
  } finally {
    deleting.value = false;
  }
};

const cancelDelete = () => {
  showDeleteModal.value = false;
  courseToDelete.value = null;
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

const handleImageError = (event) => {
  // Hide the image and show the color background instead
  event.target.style.display = 'none';
};

// Lifecycle
onMounted(async () => {
  await fetchCourses();
});
</script>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>