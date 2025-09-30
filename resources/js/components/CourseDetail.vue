<template>
  <div class="course-detail bg-gray-50 min-h-screen py-8">
    <!-- Loading State -->
    <div v-if="loading" class="flex flex-col justify-center items-center py-16">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      <span class="mt-4 text-gray-500 text-base">Cargando curso...</span>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded p-4 mb-8 flex items-center gap-3">
      <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
      </svg>
      <div>
        <h3 class="text-base font-medium text-red-800">Error al cargar el curso</h3>
        <p class="text-sm text-red-700 mt-1">{{ error }}</p>
      </div>
    </div>

    <!-- Course Detail -->
  <div v-else-if="course" class="space-y-8">
      <!-- Breadcrumb -->
      <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
          <li>
            <router-link to="/" class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-blue-600">
              <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10"/></svg>
              Cursos
            </router-link>
          </li>
          <li aria-current="page">
            <span class="text-sm font-medium text-gray-400">/</span>
            <span class="ml-2 text-sm font-medium text-gray-500">{{ course.name }}</span>
          </li>
        </ol>
      </nav>

      <!-- Course Header -->
      <div class="bg-white border border-gray-200 shadow-sm rounded-lg p-8">
        <div class="flex flex-col md:flex-row items-start gap-8">
          <!-- Course Image/Color -->
          <div class="w-32 h-32 rounded-lg flex items-center justify-center flex-shrink-0"
            :style="{ backgroundColor: course.color || '#6B7280' }">
            <img v-if="course.image_path" :src="course.image_path" :alt="course.name" 
              class="w-full h-full object-cover rounded-lg" />
            <div v-else class="text-white text-center">
              <svg class="h-12 w-12 mx-auto mb-2 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
            </div>
          </div>
          <!-- Course Info -->
          <div class="flex-1 min-w-0">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
              <h1 class="text-2xl font-semibold text-gray-800">{{ course.name }}</h1>
              <div class="flex gap-2">
                <router-link :to="`/courses/${course.id}/edit`"
                  class="inline-flex items-center px-4 py-2 border border-blue-600 text-blue-600 text-sm font-medium rounded hover:bg-blue-50 transition">
                  <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6.536-6.536a2 2 0 112.828 2.828L11.828 15H9v-2z"/></svg>
                  Editar
                </router-link>
                <button @click="deleteCourse" 
                  class="inline-flex items-center px-4 py-2 border border-red-600 text-red-600 text-sm font-medium rounded hover:bg-red-50 transition">
                  <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                  Eliminar
                </button>
              </div>
            </div>
            <p class="text-gray-600 text-base mb-4">{{ course.description }}</p>
            <div class="flex items-center text-sm text-gray-500 gap-6">
              <span>Creado: {{ formatDate(course.created_at) }}</span>
              <span v-if="course.updated_at !== course.created_at">
                Actualizado: {{ formatDate(course.updated_at) }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Course Lessons Section -->
      <div class="bg-white border border-gray-200 shadow-sm rounded-lg p-8">
        <CourseLessonsTable 
          :course-id="courseId" 
          ref="lessonsTable"
          @delete-lesson="handleDeleteLesson"
        />
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-700 bg-opacity-40 flex items-center justify-center z-50">
      <div class="w-full max-w-sm mx-auto bg-white border border-gray-200 rounded-lg shadow-lg p-6">
        <div class="flex flex-col items-center">
          <div class="flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-2">
            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-gray-800 mb-2">
            {{ deleteType === 'course' ? 'Confirmar eliminación del curso' : 'Confirmar eliminación de la lección' }}
          </h3>
          <p class="text-sm text-gray-500 mb-4 text-center">
            {{ deleteType === 'course' 
              ? `¿Estás seguro de que quieres eliminar el curso "${course?.name}"? Esta acción eliminará también todas sus lecciones y no se puede deshacer.`
              : `¿Estás seguro de que quieres eliminar la lección "${itemToDelete?.name}"? Esta acción no se puede deshacer.`
            }}
          </p>
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
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import { useToast } from '../composables/useToast.js';
import CourseLessonsTable from './CourseLessonsTable.vue';

const route = useRoute();
const router = useRouter();

// Props
const courseId = route.params.id;

// Estado reactivo
const course = ref(null);
const loading = ref(true);
const error = ref(null);
const showDeleteModal = ref(false);
const itemToDelete = ref(null);
const deleteType = ref(''); // 'course' or 'lesson'
const deleting = ref(false);

// Referencias
const lessonsTable = ref(null);

// Toast notifications
const { success, error: showError } = useToast();

// Métodos
const fetchCourse = async () => {
  try {
    loading.value = true;
    error.value = null;
    
    const response = await axios.get(`/api/courses/${courseId}`);
    
    if (response.data.success) {
      course.value = response.data.data;
    } else {
      error.value = response.data.message || 'Error al cargar el curso';
    }
  } catch (err) {
    console.error('Error fetching course:', err);
    error.value = err.response?.data?.message || 'Error de conexión al servidor';
  } finally {
    loading.value = false;
  }
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const deleteCourse = () => {
  deleteType.value = 'course';
  itemToDelete.value = course.value;
  showDeleteModal.value = true;
};

const handleDeleteLesson = (lesson) => {
  deleteType.value = 'lesson';
  itemToDelete.value = lesson;
  showDeleteModal.value = true;
};

const confirmDelete = async () => {
  try {
    deleting.value = true;
    
    if (deleteType.value === 'course') {
      const response = await axios.delete(`/api/courses/${course.value.id}`);
      
      if (response.data.success) {
        success('Curso eliminado exitosamente');
        router.push('/');
      } else {
        showError(response.data.message || 'Error al eliminar el curso');
      }
    } else {
      const response = await axios.delete(`/api/courses/${courseId}/lessons/${itemToDelete.value.id}`);
      
      if (response.data.success) {
        success('Lección eliminada exitosamente');
        // Refresh lessons table
        if (lessonsTable.value) {
          lessonsTable.value.fetchLessons();
        }
      } else {
        showError(response.data.message || 'Error al eliminar la lección');
      }
    }
  } catch (err) {
    console.error('Error deleting:', err);
    showError(err.response?.data?.message || 'Error de conexión al servidor');
  } finally {
    deleting.value = false;
    cancelDelete();
  }
};

const cancelDelete = () => {
  showDeleteModal.value = false;
  itemToDelete.value = null;
  deleteType.value = '';
};

// Lifecycle
onMounted(() => {
  fetchCourse();
});
</script>