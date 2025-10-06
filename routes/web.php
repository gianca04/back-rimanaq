<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\CourseWebController;

// Ruta raíz - redirige al login
Route::get('/', function () {
    return redirect()->route('web.dashboard');
})->name('web.dashboard');

// Ruta de login
Route::get('/login', function () {
    return view('auth.login');
})->name('web.login');

// Ruta básica de dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('web.dashboard');

// Rutas para la vista de lecciones
Route::get('/dashboard/lessons', function () {
    return view('lesson.index');
})->name('web.lessons.index');

Route::get('/dashboard/lessons/create', function () {
    return view('lesson.create');
})->name('web.lessons.create');

Route::get('/dashboard/lessons/edit/{id}', function ($id) {
    return view('lesson.edit', compact('id'));
})->name('web.lessons.edit');

// Rutas para la vista de gestos
Route::get('/dashboard/gestures', function () {
    return view('gesture.index');
})->name('web.gestures.index');

Route::get('/dashboard/gestures/create', function () {
    return view('gesture.create');
})->name('web.gestures.create');

Route::get('/dashboard/gestures/edit/{id}', function ($id) {
    return view('gesture.edit', compact('id'));
})->name('web.gestures.edit');

// Rutas para la vista de cursos
Route::get('/dashboard/courses', function () {
    return view('course.index');
})->name('web.courses.index');

Route::get('/dashboard/courses/create', function () {
    return view('course.create');
})->name('web.courses.create');

Route::get('/dashboard/courses/edit/{id}', function ($id) {
    return view('course.edit', compact('id'));
})->name('web.courses.edit');
