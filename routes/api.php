<?php

use App\Http\Controllers\API\V1\GestureController;
use App\Http\Controllers\API\V1\LessonController;
use App\Http\Controllers\API\V1\ProgressController;
use App\Http\Controllers\API\V1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\CourseController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Aquí se registran las rutas de la API.
| Estas rutas son cargadas por RouteServiceProvider y estarán bajo el 
| grupo "api" y el prefijo "/api".
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json($request->user());
});

// Rutas públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Ruta GET login para APIs (devuelve 401 cuando no está autenticado)
Route::get('/login', function () {
    return response()->json(['message' => 'Unauthenticated.'], 401);
});


// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Rutas de usuarios
    Route::get('users', [UserController::class, 'index']);
    Route::get('users/{user}', [UserController::class, 'show']);
    
    /*
    |--------------------------------------------------------------------------
    | Rutas de Recursos Principales
    |--------------------------------------------------------------------------
    | Rutas RESTful completas para los recursos principales del sistema
    */

    // Cursos - CRUD completo
    Route::apiResource('courses', CourseController::class)->names([
        'index' => 'courses.index',
        'store' => 'courses.store',
        'show' => 'courses.show',
        'update' => 'courses.update',
        'destroy' => 'courses.destroy'
    ]);

    // Lecciones - CRUD completo
    Route::apiResource('lessons', LessonController::class)->names([
        'index' => 'lessons.index',
        'store' => 'lessons.store',
        'show' => 'lessons.show',
        'update' => 'lessons.update',
        'destroy' => 'lessons.destroy'
    ]);

    // Gestos - CRUD completo
    Route::apiResource('gestures', GestureController::class)->names([
        'index' => 'gestures.index',
        'store' => 'gestures.store',
        'show' => 'gestures.show',
        'update' => 'gestures.update',
        'destroy' => 'gestures.destroy'
    ]);

    // Progreso - CRUD completo
    Route::apiResource('progress', ProgressController::class)->names([
        'index' => 'progress.index',
        'store' => 'progress.store',
        'show' => 'progress.show',
        'update' => 'progress.update',
        'destroy' => 'progress.destroy'
    ]);

    /*
    |--------------------------------------------------------------------------
    | Rutas de Relaciones y Consultas Específicas
    |--------------------------------------------------------------------------
    | Rutas para obtener datos relacionados entre recursos
    */

    // Lecciones de un curso específico
    Route::get('courses/{course}/lessons', [LessonController::class, 'getByCourse'])
        ->name('courses.lessons')
        ->whereNumber('course');

    // Gestos de una lección específica
    Route::get('lessons/{lesson}/gestures', [GestureController::class, 'getByLesson'])
        ->name('lessons.gestures')
        ->whereNumber('lesson');

    /*
    |--------------------------------------------------------------------------
    | Rutas de Progreso y Estadísticas
    |--------------------------------------------------------------------------
    | Rutas específicas para consultar y actualizar progreso de usuarios
    */

    // Progreso por usuario
    Route::get('users/{user}/progress', [ProgressController::class, 'getByUser'])
        ->name('users.progress')
        ->whereNumber('user');

    // Progreso por curso
    Route::get('courses/{course}/progress', [ProgressController::class, 'getByCourse'])
        ->name('courses.progress')
        ->whereNumber('course');

    // Progreso por lección
    Route::get('lessons/{lesson}/progress', [ProgressController::class, 'getByLesson'])
        ->name('lessons.progress')
        ->whereNumber('lesson');

    /*
    |--------------------------------------------------------------------------
    | Acciones Específicas de Progreso
    |--------------------------------------------------------------------------
    | Rutas para acciones específicas del sistema de progreso
    */

    // Marcar lección como completada
    Route::post('progress/mark-completed', [ProgressController::class, 'markCompleted'])
        ->name('progress.mark-completed');

    // Incrementar intentos de una lección
    Route::post('progress/increment-attempts', [ProgressController::class, 'incrementAttempts'])
        ->name('progress.increment-attempts');

    // Aquí puedes mover las rutas de cursos si quieres que estén protegidas
    // Route::apiResource('courses', App\Http\Controllers\API\CourseController::class);
});
