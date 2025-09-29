# 🚀 Guía completa: Laravel 12 + Vue 3 + Vite + Tailwind

## 📋 Requisitos previos
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL/MariaDB

## 🔧 1. Instalación inicial de Laravel

```bash
# Crear nuevo proyecto Laravel
laravel new rimanaq

# Navegar al directorio
cd rimanaq

# Configurar política de ejecución (Windows PowerShell)
Set-ExecutionPolicy -Scope CurrentUser -ExecutionPolicy Bypass
```

## 📦 2. Instalación de dependencias Vue

```bash
# Instalar Vue 3 y dependencias relacionadas
npm install vue@^3.5.22
npm install vue-router@^4.5.1
npm install axios@^1.11.0

# Instalar plugin de Vue para Vite
npm install @vitejs/plugin-vue --save-dev
npm install @types/node --save-dev
```

## ⚙️ 3. Configuración de Vite

Actualizar `vite.config.js`:

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
});
```

## 🎯 4. Configuración del punto de entrada Vue

Actualizar `resources/js/app.js`:

```javascript
import './bootstrap';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import App from './App.vue';
import axios from 'axios';

// Configurar axios globalmente
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';

// Configurar CSRF token
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

// Importar componentes
import CoursesList from './components/CoursesList.vue';
import CourseForm from './components/CourseForm.vue';

// Configurar rutas
const routes = [
    { 
        path: '/', 
        name: 'courses', 
        component: CoursesList 
    },
    { 
        path: '/courses/create', 
        name: 'courses.create', 
        component: CourseForm 
    },
    { 
        path: '/courses/:id/edit', 
        name: 'courses.edit', 
        component: CourseForm,
        props: true 
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

// Crear y montar aplicación Vue
const app = createApp(App);
app.use(router);
app.config.globalProperties.$http = axios;
app.mount('#app');
```

## 📄 5. Crear vista Blade para SPA

Crear `resources/views/vue.blade.php`:

```php
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Rimanaq') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div id="app"></div>
    </body>
</html>
```

## 🛣️ 6. Configurar rutas web

Actualizar `routes/web.php`:

```php
<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('vue');
});

Route::get('{any}', function () {
    return view('vue');
})->where('any', '.*');
```

## 🎨 7. Crear componente raíz Vue

Crear `resources/js/App.vue`:

```vue
<template>
  <div id="app" class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b">
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
          <div class="flex items-center space-x-4">
            <router-link 
              to="/" 
              class="text-gray-600 hover:text-gray-800 px-3 py-2 rounded-md text-sm font-medium"
              :class="{ 'text-blue-600 bg-blue-50': $route.name === 'courses' }"
            >
              Cursos
            </router-link>
            <router-link 
              to="/courses/create" 
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium"
            >
              Nuevo Curso
            </router-link>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <router-view />
    </main>

    <!-- Loading Spinner -->
    <div v-if="loading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
        <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-gray-700">Cargando...</span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'App',
  data() {
    return {
      loading: false
    }
  },
  methods: {
    setLoading(state) {
      this.loading = state;
    }
  },
  provide() {
    return {
      setLoading: this.setLoading
    }
  }
}
</script>
```

## 🗃️ 8. Configurar backend Laravel

### 8.1 Crear modelo y migración

```bash
# Crear modelo Course con migración, factory y controlador
php artisan make:model Course -mcr

# Crear controlador API específico
php artisan make:controller API/CourseController --api --resource
```

### 8.2 Configurar migración

Actualizar la migración de courses:

```php
Schema::create('courses', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description');
    $table->string('image_path')->nullable();
    $table->timestamps();
});
```

### 8.3 Configurar modelo Course

Actualizar `app/Models/Course.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'description',
        'image_path',
    ];
}
```

### 8.4 Configurar controlador API

Actualizar `app/Http/Controllers/API/CourseController.php`:

```php
<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class CourseController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $courses = Course::orderBy('created_at', 'desc')->get();
            
            return response()->json([
                'success' => true,
                'data' => $courses,
                'message' => 'Cursos obtenidos exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los cursos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'image_path' => 'nullable|string|max:255',
            ]);

            $course = Course::create($validated);

            return response()->json([
                'success' => true,
                'data' => $course,
                'message' => 'Curso creado exitosamente'
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function show(Course $course): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $course,
            'message' => 'Curso obtenido exitosamente'
        ]);
    }

    public function update(Request $request, Course $course): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'image_path' => 'nullable|string|max:255',
            ]);

            $course->update($validated);

            return response()->json([
                'success' => true,
                'data' => $course,
                'message' => 'Curso actualizado exitosamente'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function destroy(Course $course): JsonResponse
    {
        try {
            $course->delete();

            return response()->json([
                'success' => true,
                'message' => 'Curso eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el curso',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
```

### 8.5 Configurar rutas API

Actualizar `routes/api.php`:

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json($request->user());
});

// Rutas de cursos (públicas por ahora)
Route::apiResource('courses', App\Http\Controllers\API\CourseController::class);
```

## 🎯 9. Configurar Laravel Sanctum (Opcional)

### 9.1 Instalar Sanctum

```bash
# Sanctum ya viene incluido en Laravel 12
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### 9.2 Configurar modelo User

Actualizar `app/Models/User.php`:

```php
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;
    // ... resto del código
}
```

## 🚀 10. Ejecutar la aplicación

### 10.1 Preparar base de datos

```bash
# Configurar .env con datos de BD
cp .env.example .env
php artisan key:generate

# Ejecutar migraciones
php artisan migrate
```

### 10.2 Iniciar servidores

```bash
# Terminal 1: Servidor Laravel
php artisan serve

# Terminal 2: Servidor Vite
npm run dev
```

### 10.3 Acceder a la aplicación

- **URL:** `http://localhost:8000`
- **API:** `http://localhost:8000/api/courses`

## 📁 11. Estructura de archivos final

```
rimanaq/
├── resources/
│   ├── js/
│   │   ├── App.vue
│   │   ├── app.js
│   │   └── components/
│   │       ├── CoursesList.vue
│   │       └── CourseForm.vue
│   ├── css/
│   │   └── app.css
│   └── views/
│       └── vue.blade.php
├── app/
│   ├── Http/Controllers/API/
│   │   └── CourseController.php
│   └── Models/
│       └── Course.php
├── routes/
│   ├── web.php
│   └── api.php
├── vite.config.js
└── package.json
```

## 🎨 12. Estilos personalizados

### 12.1 En `resources/css/app.css`:

```css
@tailwind base;
@tailwind components;
@tailwind utilities;

@layer components {
  .btn-primary {
    @apply px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors;
  }
  
  .card {
    @apply bg-white rounded-xl shadow-sm border border-gray-200 p-6;
  }
}
```

### 12.2 En componentes Vue (scoped):

```vue
<style scoped>
.custom-component {
  @apply bg-gradient-to-r from-blue-500 to-purple-600;
}
</style>
```

## ✅ 13. Verificar instalación

- [ ] Laravel serve funciona
- [ ] Vite dev server funciona
- [ ] Vue Router navegación funciona
- [ ] API endpoints responden
- [ ] CRUD de cursos funcional
- [ ] Estilos Tailwind aplicados

## 🔧 14. Comandos útiles

```bash
# Desarrollo
npm run dev              # Servidor Vite
php artisan serve        # Servidor Laravel

# Construcción
npm run build           # Build para producción

# Base de datos
php artisan migrate     # Ejecutar migraciones
php artisan migrate:fresh --seed  # Resetear BD

# Caché
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## 🐛 15. Troubleshooting común

- **Error CSRF:** Verificar meta tag csrf-token
- **Error 404 en rutas Vue:** Verificar configuración catch-all
- **Axios no funciona:** Verificar configuración axios global
- **Vite no recarga:** Verificar puerto y configuración HMR
- **Estilos no aplican:** Verificar importación de CSS en app.js