<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('vue');
});

// Comentamos la ruta comodín temporalmente para que no interfiera con la API
// Route::get('{any}', function () {
//     return view('vue');
// })->where('any', '^(?!api).*$');