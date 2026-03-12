<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Thehouseofel\Kalion\Features\AuthFlow\Infrastructure\Controllers\LoginController;
use Thehouseofel\Kalion\Features\AuthFlow\Infrastructure\Controllers\PasswordResetController;
use Thehouseofel\Kalion\Features\AuthFlow\Infrastructure\Controllers\RegisterController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);

    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    Route::get('/forgot-password', [PasswordResetController::class, 'create'])->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
});
