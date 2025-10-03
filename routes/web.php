<?php

use Illuminate\Support\Facades\Route;

// Ruta de login



Route::get('/login', function () {
    return view('auth.login');
})->name('web.login');

// Ruta básica de dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('web.dashboard');

// Ruta para la vista de cursos
Route::get('/dashboard/courses', function () {
    return view('course.index');
})->name('web.courses.index');
