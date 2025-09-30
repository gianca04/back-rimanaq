<template>
  <div id="app" class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav v-if="!isAuthPage" class="bg-white shadow-sm border-b">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <router-link 
              to="/" 
              class="text-xl font-bold text-gray-800 hover:text-gray-600"
            >
              Rimanaq
            </router-link>
          </div>
          
          <!-- Menu for authenticated users -->
          <div v-if="isAuthenticated" class="flex items-center space-x-4">
            <router-link 
              to="/" 
              class="text-gray-600 hover:text-gray-800 px-3 py-2 rounded-md text-sm font-medium"
              :class="{ 'text-blue-600 bg-blue-50': $route.name === 'courses' }"
            >
              Cursos
            </router-link>
            <router-link 
              to="/lessons" 
              class="text-gray-600 hover:text-gray-800 px-3 py-2 rounded-md text-sm font-medium"
              :class="{ 'text-blue-600 bg-blue-50': $route.name === 'all-lessons' }"
            >
              Lecciones
            </router-link>
            <router-link 
              to="/courses/create" 
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium"
            >
              Nuevo Curso
            </router-link>
            
            <!-- User menu -->
            <div class="relative">
              <span class="text-gray-700 text-sm">{{ userName }}</span>
              <button 
                @click="handleLogout"
                class="ml-3 text-gray-500 hover:text-gray-700 text-sm font-medium"
              >
                Cerrar Sesión
              </button>
            </div>
          </div>
          
          <!-- Menu for guests -->
          <div v-else class="flex items-center space-x-4">
            <router-link 
              to="/login" 
              class="text-gray-600 hover:text-gray-800 px-3 py-2 rounded-md text-sm font-medium"
            >
              Iniciar Sesión
            </router-link>
            <router-link 
              to="/register" 
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium"
            >
              Registrarse
            </router-link>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <router-view />
    </main>

    <!-- Loading Spinner (global) -->
    <div v-if="loading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
        <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-gray-700">Cargando...</span>
      </div>
    </div>

    <!-- Toast Notifications -->
    <ToastNotification 
      v-for="toast in toasts" 
      :key="toast.id"
      :show="toast.show"
      :message="toast.message"
      :type="toast.type"
      :duration="0"
      @close="removeToast(toast.id)"
    />
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuth } from './composables/useAuth.js';
import { useToast } from './composables/useToast.js';
import ToastNotification from './components/ToastNotification.vue';

const route = useRoute();
const router = useRouter();
const { isAuthenticated, userName, loading, logout, initAuth } = useAuth();
const { toasts, removeToast } = useToast();

// Verificar si estamos en una página de autenticación
const isAuthPage = computed(() => {
  return ['login', 'register'].includes(route.name);
});

// Manejar logout
const handleLogout = async () => {
  await logout();
  router.push({ name: 'login' });
};

// Inicializar autenticación al montar el componente
onMounted(() => {
  initAuth();
});
</script>

<style>
/* Estilos adicionales si son necesarios */
</style>