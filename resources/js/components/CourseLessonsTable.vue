<template>
  <div class="course-lessons-table bg-gray-50 py-4">
    <div class="mb-6 flex items-center justify-between">
      <h3 class="text-xl font-semibold text-gray-800">Lecciones del Curso ({{ lessons.length }})</h3>
      <router-link 
        :to="`/courses/${courseId}/lessons/create`"
        class="inline-flex items-center px-4 py-2 border border-blue-600 text-blue-600 text-sm font-medium rounded hover:bg-blue-50 transition"
      >
        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nueva Lección
      </router-link>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex flex-col justify-center items-center py-12">
      <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
      <span class="mt-4 text-gray-500 text-base">Cargando lecciones...</span>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded p-4 flex items-center gap-3 mb-4">
      <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
      </svg>
      <p class="text-sm text-red-700">{{ error }}</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="lessons.length === 0" class="text-center py-12">
      <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      <h4 class="mt-4 text-base font-semibold text-gray-800">Sin lecciones</h4>
      <p class="mt-2 text-sm text-gray-500">Este curso aún no tiene lecciones.</p>
    </div>

    <!-- Lessons Table -->
    <div v-else class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-lg">
      <table class="min-w-full divide-y divide-gray-100">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nivel</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Lección</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Dificultad</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Duración</th>
            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Acciones</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
          <tr v-for="lesson in sortedLessons" :key="lesson.id" class="hover:bg-gray-50">
            <td class="px-5 py-4 whitespace-nowrap">
              <div class="text-base font-medium text-gray-800">{{ lesson.level_number }}</div>
            </td>
            <td class="px-5 py-4">
              <div class="text-base font-medium text-gray-800">{{ lesson.name }}</div>
              <div class="text-sm text-gray-500 max-w-xs truncate">{{ lesson.description }}</div>
            </td>
            <td class="px-5 py-4 whitespace-nowrap">
              <span 
                class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                :class="getDifficultyClasses(lesson.difficulty)"
              >
                {{ lesson.difficulty }}
              </span>
            </td>
            <td class="px-5 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-700">{{ lesson.time_minutes }} min</div>
            </td>
            <td class="px-5 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div class="flex justify-end gap-2">
                <router-link 
                  :to="`/courses/${courseId}/lessons/${lesson.id}/edit`"
                  class="text-blue-600 hover:text-blue-800 transition text-xs"
                >
                  <span class="inline-flex items-center gap-1">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Editar
                  </span>
                </router-link>
                <button 
                  @click="$emit('delete-lesson', lesson)"
                  class="text-red-600 hover:text-red-800 transition text-xs"
                >
                  <span class="inline-flex items-center gap-1">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Eliminar
                  </span>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
  courseId: {
    type: [String, Number],
    required: true
  }
});

const emit = defineEmits(['delete-lesson']);

// Estado reactivo
const lessons = ref([]);
const loading = ref(true);
const error = ref(null);

// Computed
const sortedLessons = computed(() => {
  return [...lessons.value].sort((a, b) => a.level_number - b.level_number);
});

// Métodos
const fetchLessons = async () => {
  try {
    loading.value = true;
    error.value = null;
    
    const response = await axios.get(`/api/courses/${props.courseId}/lessons`);
    
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

// Watchers
watch(() => props.courseId, () => {
  if (props.courseId) {
    fetchLessons();
  }
}, { immediate: true });

// Lifecycle
onMounted(() => {
  if (props.courseId) {
    fetchLessons();
  }
});

// Expose method to refresh lessons
defineExpose({
  fetchLessons
});
</script>