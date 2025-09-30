<template>
  <div class="lessons-list">
    <!-- Header -->
    <div class="mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">
            Lecciones {{ courseData ? `- ${courseData.name}` : '' }}
          </h1>
          <p class="mt-2 text-gray-600">
            Administra las lecciones de este curso
          </p>
        </div>
        <div class="flex space-x-3">
          <router-link 
            to="/"
            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
          >
            Volver a Cursos
          </router-link>
          <router-link 
            :to="`/courses/${courseId}/lessons/create`"
            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
          >
            Nueva Lección
          </router-link>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      <span class="ml-2 text-gray-600">Cargando lecciones...</span>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <h3 class="text-sm font-medium text-red-800">Error al cargar las lecciones</h3>
          <p class="text-sm text-red-700 mt-1">{{ error }}</p>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="lessons.length === 0" class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      <h3 class="mt-4 text-lg font-medium text-gray-900">No hay lecciones disponibles</h3>
      <p class="mt-2 text-gray-500">Comienza creando la primera lección de este curso.</p>
      <div class="mt-6">
        <router-link 
          :to="`/courses/${courseId}/lessons/create`"
          class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
        >
          <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
          </svg>
          Crear Primera Lección
        </router-link>
      </div>
    </div>

    <!-- Lessons List -->
    <div v-else class="space-y-4">
      <div 
        v-for="lesson in lessons" 
        :key="lesson.id" 
        class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow duration-200"
      >
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <div class="flex items-center space-x-3 mb-2">
              <span 
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                :class="getDifficultyClasses(lesson.difficulty)"
              >
                {{ lesson.difficulty }}
              </span>
              <span class="text-sm text-gray-500">
                Nivel {{ lesson.level_number }}
              </span>
              <span class="text-sm text-gray-500">
                {{ lesson.time_minutes }} min
              </span>
            </div>
            
            <h3 class="text-lg font-medium text-gray-900 mb-2">
              {{ lesson.name }}
            </h3>
            
            <p class="text-gray-600 text-sm mb-3">
              {{ lesson.description }}
            </p>
            
            <div class="text-xs text-gray-500">
              Creada: {{ formatDate(lesson.created_at) }}
            </div>
          </div>
          
          <!-- Actions -->
          <div class="flex space-x-2 ml-4">
            <router-link 
              :to="`/courses/${courseId}/lessons/${lesson.id}/edit`"
              class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50"
            >
              Editar
            </router-link>
            <button 
              @click="deleteLesson(lesson)"
              class="inline-flex items-center px-3 py-1 border border-red-300 shadow-sm text-xs font-medium rounded text-red-700 bg-white hover:bg-red-50"
            >
              Eliminar
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mt-4">Confirmar eliminación</h3>
          <div class="mt-2 px-7 py-3">
            <p class="text-sm text-gray-500">
              ¿Estás seguro de que quieres eliminar la lección "{{ lessonToDelete?.name }}"? Esta acción no se puede deshacer.
            </p>
          </div>
          <div class="items-center px-4 py-3">
            <button 
              @click="confirmDelete"
              :disabled="deleting"
              class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-red-700 disabled:opacity-50"
            >
              {{ deleting ? 'Eliminando...' : 'Eliminar' }}
            </button>
            <button 
              @click="cancelDelete"
              class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-24 hover:bg-gray-400"
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
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import { useToast } from '../composables/useToast.js';

const route = useRoute();
const { success, error: showError } = useToast();

// Props del route
const courseId = computed(() => route.params.courseId);

// Estado reactivo
const lessons = ref([]);
const courseData = ref(null);
const loading = ref(true);
const error = ref(null);
const showDeleteModal = ref(false);
const lessonToDelete = ref(null);
const deleting = ref(false);

// Métodos
const fetchCourse = async () => {
  try {
    const response = await axios.get(`/api/courses/${courseId.value}`);
    if (response.data.success) {
      courseData.value = response.data.data;
    }
  } catch (err) {
    console.error('Error fetching course:', err);
  }
};

const fetchLessons = async () => {
  try {
    loading.value = true;
    error.value = null;
    
    const response = await axios.get(`/api/courses/${courseId.value}/lessons`);
    
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

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
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
    fetchCourse(),
    fetchLessons()
  ]);
});
</script>