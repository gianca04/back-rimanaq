<template>
  <nav class="bg-white shadow-sm border-b sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <!-- Logo y título -->
        <div class="flex items-center">
          <router-link 
            to="/" 
            class="flex-shrink-0 text-xl font-bold text-gray-800 hover:text-gray-600 transition-colors duration-200"
          >
            Rimanaq
          </router-link>
        </div>
        
        <!-- Desktop Navigation -->
        <div class="flex items-center space-x-2">
          <!-- Debug info -->
          <div class="text-xs text-red-500 mr-2">Auth: {{ isAuthenticated ? 'Yes' : 'No' }} | User: {{ userName || 'None' }}</div>
          <template v-if="isAuthenticated">
            <!-- Navigation Links -->
            <router-link 
              to="/" 
              class="text-gray-600 hover:text-gray-800 hover:bg-gray-50 px-3 py-2 rounded-md text-sm font-medium transition-all duration-200"
              :class="isActiveRoute('courses')"
            >
              Cursos
            </router-link>
            <router-link 
              to="/lessons" 
              class="text-gray-600 hover:text-gray-800 hover:bg-gray-50 px-3 py-2 rounded-md text-sm font-medium transition-all duration-200"
              :class="isActiveRoute('all-lessons')"
            >
              Lecciones
            </router-link>
            <router-link 
              to="/courses/create" 
              class="inline-flex items-center bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 shadow-sm hover:shadow-md"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
              Nuevo Curso
            </router-link>
            
            <!-- User Menu -->
            <div class="relative ml-4 pl-4 border-l border-gray-200">
              <div class="flex items-center space-x-3">
                <div class="flex items-center space-x-2">
                  <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                    <span class="text-blue-600 font-medium text-sm">{{ userInitials }}</span>
                  </div>
                  <span class="text-sm text-gray-700 font-medium">{{ userName }}</span>
                </div>
                <button 
                  @click="handleLogout"
                  class="inline-flex items-center text-gray-600 hover:text-gray-800 hover:bg-gray-50 active:bg-gray-100 px-3 py-2 rounded-md text-sm font-medium transition-all duration-200 border border-gray-200 hover:border-gray-300"
                >
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                  </svg>
                  Salir
                </button>
              </div>
            </div>
          </template>
          
          <!-- Guest Menu -->
          <template v-else>
            <div class="text-xs text-blue-500 mr-2">Showing guest menu</div>
            <router-link 
              to="/login" 
              class="inline-flex items-center text-gray-600 hover:text-gray-800 hover:bg-gray-50 active:bg-gray-100 px-3 py-2 rounded-md text-sm font-medium transition-all duration-200 border border-gray-200 hover:border-gray-300"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7c2 0 3 1 3 3v1"/>
              </svg>
              Iniciar Sesión
            </router-link>
            <router-link 
              to="/register" 
              class="inline-flex items-center bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 shadow-sm hover:shadow-md"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
              </svg>
              Registrarse
            </router-link>
          </template>
        </div>

        <!-- Mobile menu button -->
        <div class="hidden flex items-center">
          <button 
            @click="toggleMobileMenu"
            class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 transition-colors duration-200"
            :aria-expanded="mobileMenuOpen"
            aria-label="Abrir menú principal"
          >
            <svg class="h-6 w-6" :class="{ 'hidden': mobileMenuOpen, 'block': !mobileMenuOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg class="h-6 w-6" :class="{ 'block': mobileMenuOpen, 'hidden': !mobileMenuOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div class="md:hidden" :class="{ 'block': mobileMenuOpen, 'hidden': !mobileMenuOpen }">
      <div class="px-2 pt-2 pb-3 space-y-1 bg-white border-t border-gray-200">
        <template v-if="isAuthenticated">
          <router-link 
            to="/" 
            class="block px-3 py-2 text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded-md transition-colors duration-200"
            :class="isActiveRoute('courses')"
            @click="closeMobileMenu"
          >
            Cursos
          </router-link>
          <router-link 
            to="/lessons" 
            class="block px-3 py-2 text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded-md transition-colors duration-200"
            :class="isActiveRoute('all-lessons')"
            @click="closeMobileMenu"
          >
            Lecciones
          </router-link>
          <router-link 
            to="/courses/create" 
            class="block px-3 py-2 text-base font-medium text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-md transition-colors duration-200"
            @click="closeMobileMenu"
          >
            Nuevo Curso
          </router-link>
          
          <!-- User info in mobile -->
          <div class="px-3 py-2 border-t border-gray-200 mt-3">
            <div class="flex items-center space-x-2 mb-3">
              <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                <span class="text-blue-600 font-medium text-sm">{{ userInitials }}</span>
              </div>
              <div class="text-sm text-gray-700 font-medium">{{ userName }}</div>
            </div>
            <button 
              @click="handleLogout"
              class="inline-flex items-center w-full text-left text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-50 active:bg-gray-100 px-3 py-2 rounded-md font-medium transition-all duration-200 border border-gray-200 hover:border-gray-300"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
              </svg>
              Cerrar Sesión
            </button>
          </div>
        </template>
        
        <!-- Guest Menu Mobile -->
        <template v-else>
          <router-link 
            to="/login" 
            class="inline-flex items-center w-full text-gray-600 hover:text-gray-800 hover:bg-gray-50 active:bg-gray-100 px-3 py-2 rounded-md text-base font-medium transition-all duration-200 border border-gray-200 hover:border-gray-300 mb-2"
            @click="closeMobileMenu"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7c2 0 3 1 3 3v1"/>
            </svg>
            Iniciar Sesión
          </router-link>
          <router-link 
            to="/register" 
            class="inline-flex items-center w-full bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white px-3 py-2 rounded-md text-base font-medium transition-all duration-200 shadow-sm hover:shadow-md"
            @click="closeMobileMenu"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            Registrarse
          </router-link>
        </template>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuth } from '../composables/useAuth.js';

const route = useRoute();
const router = useRouter();
const { isAuthenticated, userName, logout } = useAuth();

// Debug: Watch authentication changes
watch([isAuthenticated, userName], ([auth, name]) => {
  console.log('Navbar - Auth changed:', { auth, name });
}, { immediate: true });

// Mobile menu state
const mobileMenuOpen = ref(false);

// Computed properties
const isActiveRoute = (routeName) => {
  return route.name === routeName ? 'text-blue-600 bg-blue-50' : '';
};

const userInitials = computed(() => {
  if (!userName.value) return '';
  return userName.value.split(' ').map(name => name.charAt(0)).join('').toUpperCase().slice(0, 2);
});

// Methods
const toggleMobileMenu = () => {
  mobileMenuOpen.value = !mobileMenuOpen.value;
};

const closeMobileMenu = () => {
  mobileMenuOpen.value = false;
};

const handleLogout = async () => {
  await logout();
  closeMobileMenu();
  router.push({ name: 'login' });
};

// Close mobile menu when route changes
const unwatchRoute = router.afterEach(() => {
  closeMobileMenu();
});

// Cleanup
import { onBeforeUnmount } from 'vue';
onBeforeUnmount(() => {
  unwatchRoute();
});
</script>

<style scoped>
/* Usando Tailwind CSS directamente - estilos mínimos */
</style>