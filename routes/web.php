<?php

use Illuminate\Support\Facades\Route;

// Ruta de login
Route::get('/', function () {
    return view('auth.login');
})->name('web.login');

// Ruta básica de dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('web.dashboard');

// Comentamos la ruta comodín temporalmente para que no interfiera con la API
// Route::get('{any}', function () {
//     return view('vue');
// })->where('any', '^(?!api).*$');