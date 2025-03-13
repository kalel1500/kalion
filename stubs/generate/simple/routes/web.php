<?php

use Illuminate\Support\Facades\Route;
use Src\Admin\Infrastructure\Http\Controllers\AdminController;
use Src\Admin\Infrastructure\Http\Controllers\AjaxAdminController;
use Src\Dashboard\Infrastructure\Http\Controllers\DashboardController;
use Src\Shared\Infrastructure\Http\Controllers\DefaultController;

/**
 * Ruta original de Laravel para la vista welcome
 */

Route::get('/welcome', fn() => view('welcome'))->name('welcome');

/**
 * Rutas de la aplicación
 */

// Ruta base
Route::redirect('/', default_url());

// Rutas protegidas
Route::middleware(['auth'])->group(function () {
    Route::get('/home',                 [DefaultController::class, 'home'])->name('home');
    Route::get('/dashboard',            [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/posts/{slug}',         [DashboardController::class, 'post'])->name('post');
    Route::get('/tags/{type?}',         [AdminController::class, 'tags'])->name('tags');
    Route::get('/fetch/tags/{type?}',   [AjaxAdminController::class, 'tags'])->name('fetch.tags');
    Route::post('/fetch/tags',          [AjaxAdminController::class, 'create'])->name('fetch.tags.create');
    Route::put('/fetch/tags/{id}',      [AjaxAdminController::class, 'update'])->name('fetch.tags.update');
    Route::delete('/fetch/tags/{id}',   [AjaxAdminController::class, 'delete'])->name('fetch.tags.delete');
});
