<template>
  <div id="app" class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <AppNavbar v-if="!isAuthPage" />

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
import { useRoute } from 'vue-router';
import { useAuth } from './composables/useAuth.js';
import { useToast } from './composables/useToast.js';
import ToastNotification from './components/ToastNotification.vue';
import AppNavbar from './components/AppNavbar.vue';

const route = useRoute();
const { loading, initAuth } = useAuth();
const { toasts, removeToast } = useToast();

// Verificar si estamos en una página de autenticación
const isAuthPage = computed(() => {
  return ['login', 'register'].includes(route.name);
});

// Inicializar autenticación al montar el componente
onMounted(() => {
  initAuth();
});
</script>

<style>
/* Estilos adicionales si son necesarios */
</style>