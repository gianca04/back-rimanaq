<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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
Route::post('/login', [AuthController::class, 'login']);

// Rutas de cursos (públicas por ahora)
Route::apiResource('courses', App\Http\Controllers\API\CourseController::class);

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    // Aquí puedes mover las rutas de cursos si quieres que estén protegidas
    // Route::apiResource('courses', App\Http\Controllers\API\CourseController::class);
});
