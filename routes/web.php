<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('vue');
});

Route::get('{any}', function () {
    return view('vue');
})->where('any', '.*');