<template>
  <div class="course-form bg-gray-50 min-h-screen py-8">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-2xl font-semibold text-gray-800">{{ isEdit ? 'Editar Curso' : 'Crear Nuevo Curso' }}</h1>
      <p class="mt-1 text-gray-500 text-base">{{ isEdit ? 'Modifica la información del curso' : 'Completa la información para crear un nuevo curso' }}</p>
    </div>

    <!-- Form -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-lg max-w-xl mx-auto">
      <form @submit.prevent="submitForm" class="px-8 py-8">
        <!-- Course Name -->
        <div class="mb-6">
          <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre del Curso *</label>
          <input id="name" v-model="form.name" type="text" required
            class="w-full px-3 py-2 border border-gray-200 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50"
            :class="{ 'border-red-500': errors.name }" placeholder="Ingresa el nombre del curso" />
          <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name[0] }}</p>
        </div>

        <!-- Course Description -->
        <div class="mb-6">
          <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descripción *</label>
          <textarea id="description" v-model="form.description" rows="4" required
            class="w-full px-3 py-2 border border-gray-200 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50"
            :class="{ 'border-red-500': errors.description }"
            placeholder="Describe de qué trata el curso, objetivos, contenido, etc."></textarea>
          <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description[0] }}</p>
        </div>

        <!-- Course Image Path -->
        <div class="mb-6">
          <label for="image_path" class="block text-sm font-medium text-gray-700 mb-2">Ruta de Imagen (Opcional)</label>
          <input id="image_path" v-model="form.image_path" type="url"
            class="w-full px-3 py-2 border border-gray-200 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50"
            :class="{ 'border-red-500': errors.image_path }" placeholder="https://ejemplo.com/imagen.jpg" />
          <p v-if="errors.image_path" class="mt-1 text-sm text-red-600">{{ errors.image_path[0] }}</p>
          <p class="mt-1 text-xs text-gray-500">URL de la imagen que representará el curso</p>
        </div>

        <!-- Course Color -->
        <div class="mb-6">
          <label for="color" class="block text-sm font-medium text-gray-700 mb-2">Color del Curso</label>
          <div class="flex items-center gap-3">
            <input id="color" v-model="form.color" type="color"
              class="h-10 w-20 border border-gray-200 rounded cursor-pointer"
              :class="{ 'border-red-500': errors.color }" />
            <input v-model="form.color" type="text"
              class="flex-1 px-3 py-2 border border-gray-200 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50"
              :class="{ 'border-red-500': errors.color }" placeholder="#3B82F6" pattern="^#[0-9A-Fa-f]{6}$" />
          </div>
          <p v-if="errors.color" class="mt-1 text-sm text-red-600">{{ errors.color[0] }}</p>
          <p class="mt-1 text-xs text-gray-500">Color hexadecimal que identificará el curso (ej: #3B82F6)</p>
        </div>

        <!-- Error Message -->
        <div v-if="generalError" class="mb-6 bg-red-50 border border-red-200 rounded p-4 flex items-center gap-3">
          <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
          <div>
            <h3 class="text-base font-medium text-red-800">Error</h3>
            <p class="text-sm text-red-700 mt-1">{{ generalError }}</p>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end gap-3 mt-8">
          <router-link to="/"
            class="px-4 py-2 border border-gray-300 rounded text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
            Cancelar
          </router-link>
          <button type="submit" :disabled="loading"
            class="px-4 py-2 border border-blue-600 rounded text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
            <span class="inline-flex items-center gap-1">
              <svg v-if="loading" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
              {{ loading ? 'Guardando...' : (isEdit ? 'Actualizar Curso' : 'Crear Curso') }}
            </span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { useToast } from '../composables/useToast.js';

const props = defineProps({
  id: {
    type: String,
    default: null
  }
});

const router = useRouter();
const { success, error: showError } = useToast();

// Estado del formulario
const form = reactive({
  name: '',
  description: '',
  image_path: '',
  color: '#3B82F6'
});

const errors = ref({});
const loading = ref(false);
const generalError = ref(null);

// Computed
const isEdit = computed(() => !!props.id);

// Métodos
const fetchCourse = async () => {
  try {
    loading.value = true;
    generalError.value = null;

    console.log('Fetching course with ID:', props.id); // Debug log

    const response = await axios.get(`/api/courses/${props.id}`);

    console.log('API Response:', response.data); // Debug log

    if (response.data.success) {
      const course = response.data.data;

      // Actualizar el formulario con los datos del curso
      Object.assign(form, {
        name: course.name || '',
        description: course.description || '',
        image_path: course.image_path || '',
        color: course.color || '#3B82F6'
      });

      console.log('Form updated with course data:', form); // Debug log
    } else {
      throw new Error(response.data.message || 'Error al cargar el curso');
    }
  } catch (error) {
    console.error('Error fetching course:', error);
    const errorMessage = error.response?.data?.message || 'Error al cargar el curso';
    generalError.value = errorMessage;
    showError(errorMessage);

    // Redirect to courses list if course not found
    if (error.response?.status === 404) {
      router.push('/');
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

    const url = isEdit.value ? `/api/courses/${props.id}` : '/api/courses';
    const method = isEdit.value ? 'put' : 'post';

    console.log('Submitting form:', { url, method, data: form }); // Debug log

    const response = await axios[method](url, form);

    if (response.data.success) {
      // Success - show message and redirect to courses list
      const message = isEdit.value ? 'Curso actualizado exitosamente' : 'Curso creado exitosamente';
      success(message);
      router.push('/');
    } else {
      throw new Error(response.data.message || 'Error al guardar el curso');
    }
  } catch (error) {
    console.error('Error submitting form:', error);

    if (error.response?.status === 422) {
      // Validation errors
      errors.value = error.response.data.errors || {};
    } else {
      const errorMessage = error.response?.data?.message || 'Error al guardar el curso';
      generalError.value = errorMessage;
      showError(errorMessage);
    }
  } finally {
    loading.value = false;
  }
};

const resetForm = () => {
  Object.assign(form, {
    name: '',
    description: '',
    image_path: '',
    color: '#3B82F6'
  });
  errors.value = {};
  generalError.value = null;
};

// Watchers
watch(() => props.id, async (newId, oldId) => {
  console.log('ID changed from', oldId, 'to', newId); // Debug log

  if (newId !== oldId) {
    if (newId) {
      await fetchCourse();
    } else {
      resetForm();
    }
  }
}, { immediate: true });

// Lifecycle
onMounted(async () => {
  console.log('Component mounted, isEdit:', isEdit.value, 'id:', props.id); // Debug log

  if (isEdit.value) {
    await fetchCourse();
  }
});
</script>

<style scoped>
/* Additional styles if needed */
</style>