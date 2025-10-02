<?php

use Illuminate\Support\Facades\Route;

// Ruta de login
Route::get('/', function () {
    return view('auth.login');
})->name('web.login');

// Rutas del dashboard (protegidas por middleware de autenticación en el frontend)
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('web.dashboard');

// Rutas principales de CRUDs
Route::get('/dashboard/courses', function () {
    return view('courses');
})->name('web.courses');

Route::get('/dashboard/lessons', function () {
    return view('lessons');
})->name('web.lessons');

Route::get('/dashboard/gestures', function () {
    return view('gestures');
})->name('web.gestures');

Route::get('/dashboard/progress', function () {
    return view('progress');
})->name('web.progress');

// Rutas jerárquicas - Lecciones de un curso específico
Route::get('/dashboard/courses/{courseId}/lessons', function ($courseId) {
    return view('course-lessons');
})->name('web.course.lessons')->where('courseId', '[0-9]+');

// Rutas jerárquicas - Gestos de una lección específica
Route::get('/dashboard/lessons/{lessonId}/gestures', function ($lessonId) {
    return view('lesson-gestures');
})->name('web.lesson.gestures')->where('lessonId', '[0-9]+');

// Comentamos la ruta comodín temporalmente para que no interfiera con la API
// Route::get('{any}', function () {
//     return view('vue');
// })->where('any', '^(?!api).*$');