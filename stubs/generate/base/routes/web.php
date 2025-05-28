<?php

use Illuminate\Support\Facades\Route;

/**
 * Ruta original de Laravel para la vista welcome
 */

Route::get('/welcome', fn() => view('welcome'))->name('welcome');

/**
 * Rutas de la aplicación
 */

// Rutas protegidas
Route::middleware(['auth'])->group(function () {
    //
});
