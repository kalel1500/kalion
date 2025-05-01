<?php

use Illuminate\Support\Facades\Route;
use Src\Home\Infrastructure\Http\Controllers\HomeController;
use Src\Posts\Infrastructure\Http\Controllers\PostController;
use Src\Tags\Infrastructure\Http\Controllers\AjaxTagController;
use Src\Tags\Infrastructure\Http\Controllers\TagController;

/**
 * Ruta original de Laravel para la vista welcome
 */

Route::get('/welcome', fn() => view('welcome'))->name('welcome');

/**
 * Rutas de la aplicación
 */

// Rutas protegidas
Route::middleware(['auth'])->group(function () {
    Route::get('/home',                 [HomeController::class, 'home'])->name('home');
    Route::get('/posts',                [PostController::class, 'list'])->name('post.list');
    Route::get('/posts/{slug}',         [PostController::class, 'detail'])->name('post.detail');
    Route::get('/tags/{type?}',         [TagController::class, 'tags'])->name('tags');
    Route::get('/fetch/tags/{type?}',   [AjaxTagController::class, 'tags'])->name('fetch.tags');
    Route::post('/fetch/tags',          [AjaxTagController::class, 'create'])->name('fetch.tags.create');
    Route::put('/fetch/tags/{id}',      [AjaxTagController::class, 'update'])->name('fetch.tags.update');
    Route::delete('/fetch/tags/{id}',   [AjaxTagController::class, 'delete'])->name('fetch.tags.delete');
});
