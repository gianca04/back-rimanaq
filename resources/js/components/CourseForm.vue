<template>
  <div class="course-form">
    <!-- Header -->
    <div class="mb-6">
      <h1 class="text-3xl font-bold text-gray-900">
        {{ isEdit ? 'Editar Curso' : 'Crear Nuevo Curso' }}
      </h1>
      <p class="mt-2 text-gray-600">
        {{ isEdit ? 'Modifica la información del curso' : 'Completa la información para crear un nuevo curso' }}
      </p>
    </div>

    <!-- Form -->
    <div class="bg-white shadow rounded-lg">
      <form @submit.prevent="submitForm" class="px-4 py-5 sm:p-6">
        <!-- Course Name -->
        <div class="mb-6">
          <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
            Nombre del Curso *
          </label>
          <input
            id="name"
            v-model="form.name"
            type="text"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            :class="{ 'border-red-500': errors.name }"
            placeholder="Ingresa el nombre del curso"
          />
          <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name[0] }}</p>
        </div>

        <!-- Course Description -->
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
            placeholder="Describe de qué trata el curso, objetivos, contenido, etc."
          ></textarea>
          <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description[0] }}</p>
        </div>

        <!-- Course Image Path -->
        <div class="mb-6">
          <label for="image_path" class="block text-sm font-medium text-gray-700 mb-2">
            Ruta de Imagen (Opcional)
          </label>
          <input
            id="image_path"
            v-model="form.image_path"
            type="url"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            :class="{ 'border-red-500': errors.image_path }"
            placeholder="https://ejemplo.com/imagen.jpg"
          />
          <p v-if="errors.image_path" class="mt-1 text-sm text-red-600">{{ errors.image_path[0] }}</p>
          <p class="mt-1 text-xs text-gray-500">
            URL de la imagen que representará el curso
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
            to="/"
            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            Cancelar
          </router-link>
          <button
            type="submit"
            :disabled="loading"
            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ loading ? 'Guardando...' : (isEdit ? 'Actualizar Curso' : 'Crear Curso') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CourseForm',
  inject: ['setLoading'],
  props: {
    id: {
      type: String,
      default: null
    }
  },
  data() {
    return {
      form: {
        name: '',
        description: '',
        image_path: ''
      },
      errors: {},
      loading: false,
      generalError: null,
      isEdit: false
    }
  },
  async mounted() {
    this.isEdit = !!this.id;
    
    if (this.isEdit) {
      await this.fetchCourse();
    }
  },
  methods: {
    async fetchCourse() {
      try {
        this.setLoading(true);
        
        const response = await this.$http.get(`/api/courses/${this.id}`);
        
        if (response.data.success) {
          const course = response.data.data;
          this.form.name = course.name;
          this.form.description = course.description;
          this.form.image_path = course.image_path || '';
        } else {
          throw new Error(response.data.message || 'Error al cargar el curso');
        }
      } catch (error) {
        console.error('Error fetching course:', error);
        this.generalError = error.response?.data?.message || 'Error al cargar el curso';
        
        // Redirect to courses list if course not found
        if (error.response?.status === 404) {
          this.$router.push('/');
        }
      } finally {
        this.setLoading(false);
      }
    },
    
    async submitForm() {
      try {
        this.loading = true;
        this.errors = {};
        this.generalError = null;
        
        const url = this.isEdit ? `/api/courses/${this.id}` : '/api/courses';
        const method = this.isEdit ? 'put' : 'post';
        
        const response = await this.$http[method](url, this.form);
        
        if (response.data.success) {
          // Success - redirect to courses list
          this.$router.push('/');
        } else {
          throw new Error(response.data.message || 'Error al guardar el curso');
        }
      } catch (error) {
        console.error('Error submitting form:', error);
        
        if (error.response?.status === 422) {
          // Validation errors
          this.errors = error.response.data.errors || {};
        } else {
          this.generalError = error.response?.data?.message || 'Error al guardar el curso';
        }
      } finally {
        this.loading = false;
      }
    },
    
    resetForm() {
      this.form = {
        name: '',
        description: '',
        image_path: ''
      };
      this.errors = {};
      this.generalError = null;
    }
  },
  
  // Watch for route changes (if navigating between edit forms)
  watch: {
    id: {
      immediate: true,
      async handler(newId, oldId) {
        if (newId !== oldId) {
          this.isEdit = !!newId;
          
          if (this.isEdit) {
            await this.fetchCourse();
          } else {
            this.resetForm();
          }
        }
      }
    }
  }
}
</script>

<style scoped>
/* Additional styles if needed */
</style>