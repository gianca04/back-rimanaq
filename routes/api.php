<?php

use App\Http\Controllers\API\V1\GestureController;
use App\Http\Controllers\API\V1\LessonController;
use App\Http\Controllers\API\V1\ProgressController;
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
    // Rutas de cursos (públicas por ahora)
    Route::apiResource('courses', CourseController::class);

    // Rutas de lecciones (públicas por ahora)
    Route::apiResource('lessons', LessonController::class);

    // Ruta para obtener lecciones de un curso específico
    Route::get('courses/{course}/lessons', [LessonController::class, 'getByCourse']);

    // Rutas de gestos (públicas por ahora)
    Route::apiResource('gestures', GestureController::class);

    // Ruta para obtener gestos de una lección específica
    Route::get('lessons/{lesson}/gestures', [GestureController::class, 'getByLesson']);

    // Rutas de progreso (públicas por ahora)
    Route::apiResource('progress', ProgressController::class);

    // Rutas específicas para consultar progreso
    Route::get('users/{user}/progress', [ProgressController::class, 'getByUser']);
    Route::get('courses/{course}/progress', [ProgressController::class, 'getByCourse']);
    Route::get('lessons/{lesson}/progress', [ProgressController::class, 'getByLesson']);

    // Rutas para acciones específicas de progreso
    Route::post('progress/mark-completed', [ProgressController::class, 'markCompleted']);
    Route::post('progress/increment-attempts', [ProgressController::class, 'incrementAttempts']);

    // Aquí puedes mover las rutas de cursos si quieres que estén protegidas
    // Route::apiResource('courses', App\Http\Controllers\API\CourseController::class);
});
