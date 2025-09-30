<template>
  <div class="all-lessons bg-gray-50 min-h-screen py-8">
    <!-- Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold text-gray-800 tracking-tight">Todas las Lecciones</h1>
          <p class="mt-1 text-gray-500 text-base">Administra todas las lecciones de tus cursos</p>
        </div>
        <router-link 
          to="/lessons/create"
          class="px-4 py-2 rounded border border-blue-600 bg-blue-600 text-white text-sm font-medium shadow hover:bg-blue-700 transition"
        >
          <span class="inline-flex items-center gap-1">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nueva Lección
          </span>
        </router-link>
      </div>
    </div>

    <!-- Search and Filters -->
    <div class="mb-8 bg-white rounded-lg border border-gray-200 shadow-sm p-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Search by name -->
        <div>
          <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
            Buscar por nombre
          </label>
          <input
            id="search"
            v-model="searchQuery"
            type="text"
            placeholder="Nombre de la lección..."
            class="w-full px-3 py-2 border border-gray-200 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50"
          />
        </div>

        <!-- Filter by course -->
        <div>
          <label for="course-filter" class="block text-sm font-medium text-gray-700 mb-1">
            Filtrar por curso
          </label>
          <select
            id="course-filter"
            v-model="selectedCourse"
            class="w-full px-3 py-2 border border-gray-200 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50"
          >
            <option value="">Todos los cursos</option>
            <option v-for="course in courses" :key="course.id" :value="course.id">
              {{ course.name }}
            </option>
          </select>
        </div>

        <!-- Filter by difficulty -->
        <div>
          <label for="difficulty-filter" class="block text-sm font-medium text-gray-700 mb-1">
            Filtrar por dificultad
          </label>
          <select
            id="difficulty-filter"
            v-model="selectedDifficulty"
            class="w-full px-3 py-2 border border-gray-200 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50"
          >
            <option value="">Todas las dificultades</option>
            <option value="fácil">Fácil</option>
            <option value="intermedio">Intermedio</option>
            <option value="difícil">Difícil</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex flex-col justify-center items-center py-16">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      <span class="mt-4 text-gray-500 text-base">Cargando lecciones...</span>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded p-4 mb-8 flex items-center gap-3">
      <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
      </svg>
      <div>
        <h3 class="text-base font-medium text-red-800">Error al cargar las lecciones</h3>
        <p class="text-sm text-red-700 mt-1">{{ error }}</p>
      </div>
    </div>

    <!-- Empty State -->
  <div v-else-if="filteredLessons.length === 0 && !searchQuery && !selectedCourse && !selectedDifficulty" class="text-center py-16">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
  <h3 class="mt-4 text-lg font-semibold text-gray-800">No hay lecciones disponibles</h3>
  <p class="mt-2 text-gray-500">Comienza creando tu primera lección.</p>
    </div>

    <!-- No Results State -->
  <div v-else-if="filteredLessons.length === 0" class="text-center py-16">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
      </svg>
  <h3 class="mt-4 text-lg font-semibold text-gray-800">No se encontraron lecciones</h3>
  <p class="mt-2 text-gray-500">Intenta con otros criterios de búsqueda.</p>
    </div>

    <!-- Lessons Table -->
    <div v-else class="bg-white border border-gray-200 shadow-sm rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-100">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Lección</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Curso</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nivel</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Dificultad</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Duración</th>
            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Acciones</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
          <tr v-for="lesson in filteredLessons" :key="lesson.id" class="hover:bg-gray-50">
            <td class="px-5 py-4">
              <div>
                <div class="text-base font-medium text-gray-800">{{ lesson.name }}</div>
                <div class="text-sm text-gray-500 max-w-xs truncate">{{ lesson.description }}</div>
              </div>
            </td>
            <td class="px-5 py-4">
              <div class="text-sm text-gray-700">{{ lesson.course?.name || 'Curso no disponible' }}</div>
            </td>
            <td class="px-5 py-4">
              <div class="text-sm text-gray-700">{{ lesson.level_number }}</div>
            </td>
            <td class="px-5 py-4">
              <span 
                class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                :class="getDifficultyClasses(lesson.difficulty)"
              >
                {{ lesson.difficulty }}
              </span>
            </td>
            <td class="px-5 py-4">
              <div class="text-sm text-gray-700">{{ lesson.time_minutes }} min</div>
            </td>
            <td class="px-5 py-4 text-right text-sm font-medium space-x-2">
              <router-link 
                :to="`/lessons/${lesson.id}/edit`"
                class="text-blue-600 hover:text-blue-800 transition"
              >
                <span class="inline-flex items-center gap-1">
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  Editar
                </span>
              </router-link>
              <button 
                @click="deleteLesson(lesson)"
                class="text-red-600 hover:text-red-800 transition"
              >
                <span class="inline-flex items-center gap-1">
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                  Eliminar
                </span>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-700 bg-opacity-40 flex items-center justify-center z-50">
      <div class="w-full max-w-sm mx-auto bg-white border border-gray-200 rounded-lg shadow-lg p-6">
        <div class="flex flex-col items-center">
          <div class="flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-2">
            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-gray-800 mb-2">Confirmar eliminación</h3>
          <p class="text-sm text-gray-500 mb-4 text-center">¿Estás seguro de que quieres eliminar la lección "{{ lessonToDelete?.name }}"? Esta acción no se puede deshacer.</p>
          <div class="flex gap-2 w-full justify-center">
            <button 
              @click="confirmDelete"
              :disabled="deleting"
              class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded hover:bg-red-700 disabled:opacity-50 transition w-24"
            >
              {{ deleting ? 'Eliminando...' : 'Eliminar' }}
            </button>
            <button 
              @click="cancelDelete"
              class="px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded hover:bg-gray-300 transition w-24"
            >
              Cancelar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { useToast } from '../composables/useToast.js';

const { success, error: showError } = useToast();

// Estado reactivo
const lessons = ref([]);
const courses = ref([]);
const loading = ref(true);
const error = ref(null);
const showDeleteModal = ref(false);
const lessonToDelete = ref(null);
const deleting = ref(false);

// Filtros
const searchQuery = ref('');
const selectedCourse = ref('');
const selectedDifficulty = ref('');

// Computed - Filtrado de lecciones
const filteredLessons = computed(() => {
  let filtered = lessons.value;

  // Filtrar por búsqueda
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(lesson => 
      lesson.name.toLowerCase().includes(query) ||
      lesson.description.toLowerCase().includes(query)
    );
  }

  // Filtrar por curso
  if (selectedCourse.value) {
    filtered = filtered.filter(lesson => lesson.course_id == selectedCourse.value);
  }

  // Filtrar por dificultad
  if (selectedDifficulty.value) {
    filtered = filtered.filter(lesson => lesson.difficulty === selectedDifficulty.value);
  }

  return filtered;
});

// Métodos
const fetchCourses = async () => {
  try {
    const response = await axios.get('/api/courses');
    if (response.data.success) {
      courses.value = response.data.data;
    }
  } catch (err) {
    console.error('Error fetching courses:', err);
  }
};

const fetchLessons = async () => {
  try {
    loading.value = true;
    error.value = null;
    
    const response = await axios.get('/api/lessons');
    
    if (response.data.success) {
      lessons.value = response.data.data;
    } else {
      error.value = response.data.message || 'Error al cargar las lecciones';
    }
  } catch (err) {
    console.error('Error fetching lessons:', err);
    error.value = err.response?.data?.message || 'Error de conexión al servidor';
  } finally {
    loading.value = false;
  }
};

const deleteLesson = (lesson) => {
  lessonToDelete.value = lesson;
  showDeleteModal.value = true;
};

const confirmDelete = async () => {
  if (!lessonToDelete.value) return;
  
  try {
    deleting.value = true;
    
    const response = await axios.delete(`/api/lessons/${lessonToDelete.value.id}`);
    
    if (response.data.success) {
      // Remove from local array
      lessons.value = lessons.value.filter(lesson => lesson.id !== lessonToDelete.value.id);
      showDeleteModal.value = false;
      lessonToDelete.value = null;
      
      success('Lección eliminada exitosamente');
    } else {
      throw new Error(response.data.message || 'Error al eliminar la lección');
    }
  } catch (err) {
    console.error('Error deleting lesson:', err);
    showError(err.response?.data?.message || 'Error al eliminar la lección');
  } finally {
    deleting.value = false;
  }
};

const cancelDelete = () => {
  showDeleteModal.value = false;
  lessonToDelete.value = null;
};

const getDifficultyClasses = (difficulty) => {
  const classes = {
    'fácil': 'bg-green-100 text-green-800',
    'facil': 'bg-green-100 text-green-800',
    'intermedio': 'bg-yellow-100 text-yellow-800',
    'difícil': 'bg-red-100 text-red-800',
    'dificil': 'bg-red-100 text-red-800'
  };
  return classes[difficulty] || 'bg-gray-100 text-gray-800';
};

// Lifecycle
onMounted(async () => {
  await Promise.all([
    fetchCourses(),
    fetchLessons()
  ]);
});
</script>