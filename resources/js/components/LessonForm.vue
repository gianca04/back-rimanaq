<template>
  <div class="lesson-form">
    <!-- Header -->
    <div class="mb-6">
      <h1 class="text-3xl font-bold text-gray-900">
        {{ isEdit ? 'Editar Lección' : 'Crear Nueva Lección' }}
      </h1>
      <p class="mt-2 text-gray-600">
        {{ isEdit ? 'Modifica la información de la lección' : 'Completa la información para crear una nueva lección' }}
      </p>
    </div>

    <!-- Form -->
    <div class="bg-white shadow rounded-lg">
      <form @submit.prevent="submitForm" class="px-4 py-5 sm:p-6">
        <!-- Lesson Name -->
        <div class="mb-6">
          <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
            Nombre de la Lección *
          </label>
          <input
            id="name"
            v-model="form.name"
            type="text"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            :class="{ 'border-red-500': errors.name }"
            placeholder="Ingresa el nombre de la lección"
          />
          <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name[0] }}</p>
        </div>

        <!-- Level Number -->
        <div class="mb-6">
          <label for="level_number" class="block text-sm font-medium text-gray-700 mb-2">
            Número de Nivel *
          </label>
          <input
            id="level_number"
            v-model.number="form.level_number"
            type="number"
            min="1"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            :class="{ 'border-red-500': errors.level_number }"
            placeholder="1"
          />
          <p v-if="errors.level_number" class="mt-1 text-sm text-red-600">{{ errors.level_number[0] }}</p>
        </div>

        <!-- Description -->
        <div class="mb-6">
          <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
            Descripción *
          </label>
          <textarea
            id="description"
            v-model="form.description"
            rows="4"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            :class="{ 'border-red-500': errors.description }"
            placeholder="Describe los objetivos y contenido de la lección"
          ></textarea>
          <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description[0] }}</p>
        </div>

        <!-- Difficulty and Time -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          <!-- Difficulty -->
          <div>
            <label for="difficulty" class="block text-sm font-medium text-gray-700 mb-2">
              Dificultad *
            </label>
            <select
              id="difficulty"
              v-model="form.difficulty"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              :class="{ 'border-red-500': errors.difficulty }"
            >
              <option value="">Selecciona la dificultad</option>
              <option value="fácil">Fácil</option>
              <option value="intermedio">Intermedio</option>
              <option value="difícil">Difícil</option>
            </select>
            <p v-if="errors.difficulty" class="mt-1 text-sm text-red-600">{{ errors.difficulty[0] }}</p>
          </div>

          <!-- Time Minutes -->
          <div>
            <label for="time_minutes" class="block text-sm font-medium text-gray-700 mb-2">
              Duración (minutos) *
            </label>
            <input
              id="time_minutes"
              v-model.number="form.time_minutes"
              type="number"
              min="1"
              max="600"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              :class="{ 'border-red-500': errors.time_minutes }"
              placeholder="30"
            />
            <p v-if="errors.time_minutes" class="mt-1 text-sm text-red-600">{{ errors.time_minutes[0] }}</p>
            <p class="mt-1 text-xs text-gray-500">
              Tiempo estimado en minutos (máximo 600 min / 10 horas)
            </p>
          </div>
        </div>

        <!-- Course Selection (for global routes) -->
        <div v-if="isGlobalRoute && !isEdit" class="mb-6">
          <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">
            Curso *
          </label>
          <select
            id="course_id"
            v-model="form.course_id"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            :class="{ 'border-red-500': errors.course_id }"
          >
            <option value="">Selecciona un curso</option>
            <option v-for="course in availableCourses" :key="course.id" :value="course.id">
              {{ course.name }}
            </option>
          </select>
          <p v-if="errors.course_id" class="mt-1 text-sm text-red-600">{{ errors.course_id[0] }}</p>
        </div>

        <!-- Course Info (if editing or course-specific route) -->
        <div v-if="courseData && (isEdit || !isGlobalRoute)" class="mb-6 bg-gray-50 border border-gray-200 rounded-md p-4">
          <p class="text-sm text-gray-700">
            <span class="font-medium">Curso:</span> {{ courseData.name }}
          </p>
        </div>

        <!-- Error Message -->
        <div v-if="generalError" class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-red-800">Error</h3>
              <p class="text-sm text-red-700 mt-1">{{ generalError }}</p>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3">
          <router-link 
            :to="isGlobalRoute ? '/lessons' : `/courses/${courseId}/lessons`"
            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            Cancelar
          </router-link>
          <button
            type="submit"
            :disabled="loading"
            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ loading ? 'Guardando...' : (isEdit ? 'Actualizar Lección' : 'Crear Lección') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';
import { useToast } from '../composables/useToast.js';

const router = useRouter();
const route = useRoute();
const { success, error: showError } = useToast();

const props = defineProps({
  id: {
    type: String,
    default: null
  }
});

// Props del route
const courseId = computed(() => route.params.courseId);
const isGlobalRoute = computed(() => route.name && route.name.startsWith('all-lessons'));

// Estado del formulario
const form = reactive({
  course_id: null,
  name: '',
  level_number: 1,
  description: '',
  difficulty: '',
  time_minutes: 30
});

const errors = ref({});
const loading = ref(false);
const generalError = ref(null);
const courseData = ref(null);
const availableCourses = ref([]);

// Computed
const isEdit = computed(() => !!props.id);

// Métodos
const fetchCourse = async () => {
  if (!courseId.value) return;
  
  try {
    const response = await axios.get(`/api/courses/${courseId.value}`);
    if (response.data.success) {
      courseData.value = response.data.data;
      form.course_id = courseData.value.id;
    }
  } catch (error) {
    console.error('Error fetching course:', error);
    showError('Error al cargar la información del curso');
  }
};

const fetchCourses = async () => {
  try {
    const response = await axios.get('/api/courses');
    if (response.data.success) {
      availableCourses.value = response.data.data;
    }
  } catch (error) {
    console.error('Error fetching courses:', error);
    showError('Error al cargar los cursos disponibles');
  }
};

const fetchLesson = async () => {
  try {
    loading.value = true;
    generalError.value = null;
    
    console.log('Fetching lesson with ID:', props.id);
    
    const response = await axios.get(`/api/lessons/${props.id}`);
    
    console.log('API Response:', response.data);
    
    if (response.data.success) {
      const lesson = response.data.data;
      
      // Actualizar el formulario con los datos de la lección
      Object.assign(form, {
        course_id: lesson.course_id,
        name: lesson.name || '',
        level_number: lesson.level_number || 1,
        description: lesson.description || '',
        difficulty: lesson.difficulty || '',
        time_minutes: lesson.time_minutes || 30
      });
      
      // Fetch course data for display
      if (lesson.course_id) {
        try {
          const courseResponse = await axios.get(`/api/courses/${lesson.course_id}`);
          if (courseResponse.data.success) {
            courseData.value = courseResponse.data.data;
          }
        } catch (courseError) {
          console.error('Error fetching course:', courseError);
        }
      }
      
      console.log('Form updated with lesson data:', form);
    } else {
      throw new Error(response.data.message || 'Error al cargar la lección');
    }
  } catch (error) {
    console.error('Error fetching lesson:', error);
    const errorMessage = error.response?.data?.message || 'Error al cargar la lección';
    generalError.value = errorMessage;
    showError(errorMessage);
    
    // Redirect to lessons list if lesson not found
    if (error.response?.status === 404) {
      const redirectPath = isGlobalRoute.value ? '/lessons' : `/courses/${courseId.value}/lessons`;
      router.push(redirectPath);
    }
  } finally {
    loading.value = false;
  }
};

const submitForm = async () => {
  try {
    loading.value = true;
    errors.value = {};
    generalError.value = null;
    
    const url = isEdit.value ? `/api/lessons/${props.id}` : '/api/lessons';
    const method = isEdit.value ? 'put' : 'post';
    
    console.log('Submitting form:', { url, method, data: form });
    
    const response = await axios[method](url, form);
    
    if (response.data.success) {
      // Success - show message and redirect to lessons list
      const message = isEdit.value ? 'Lección actualizada exitosamente' : 'Lección creada exitosamente';
      success(message);
      
      // Redirect based on route type
      const redirectPath = isGlobalRoute.value ? '/lessons' : `/courses/${courseId.value}/lessons`;
      router.push(redirectPath);
    } else {
      throw new Error(response.data.message || 'Error al guardar la lección');
    }
  } catch (error) {
    console.error('Error submitting form:', error);
    
    if (error.response?.status === 422) {
      // Validation errors
      errors.value = error.response.data.errors || {};
    } else {
      const errorMessage = error.response?.data?.message || 'Error al guardar la lección';
      generalError.value = errorMessage;
      showError(errorMessage);
    }
  } finally {
    loading.value = false;
  }
};

const resetForm = () => {
  Object.assign(form, {
    course_id: isGlobalRoute.value ? null : courseId.value,
    name: '',
    level_number: 1,
    description: '',
    difficulty: '',
    time_minutes: 30
  });
  errors.value = {};
  generalError.value = null;
};

// Watchers
watch(() => props.id, async (newId, oldId) => {
  console.log('ID changed from', oldId, 'to', newId);
  
  if (newId !== oldId) {
    if (newId) {
      await fetchLesson();
    } else {
      resetForm();
    }
  }
}, { immediate: true });

// Lifecycle
onMounted(async () => {
  console.log('Component mounted, isEdit:', isEdit.value, 'id:', props.id, 'isGlobalRoute:', isGlobalRoute.value);
  
  // Load courses for global routes
  if (isGlobalRoute.value) {
    await fetchCourses();
  }
  
  // Load course data for course-specific routes
  if (courseId.value) {
    await fetchCourse();
  }
  
  if (isEdit.value) {
    await fetchLesson();
  } else {
    // Set course_id for new lesson (only for course-specific routes)
    if (courseId.value) {
      form.course_id = parseInt(courseId.value);
    }
  }
});
</script>