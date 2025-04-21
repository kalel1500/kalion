<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Thehouseofel\Kalion\Infrastructure\Services\Kalion;

$loginController = Kalion::getClassControllerLogin();
$registerController = Kalion::getClassControllerRegister();
$passwordResetController = Kalion::getClassControllerPasswordReset();

Route::middleware('guest')->group(function () use ($loginController, $registerController, $passwordResetController) {
    Route::get('/login', [$loginController, 'create'])
        ->name('login');

    Route::post('/login', [$loginController, 'store']);

    Route::get('/register', [$registerController, 'create'])
        ->name('register');

    Route::post('/register', [$registerController, 'store']);

    Route::get('/forgot-password', [$passwordResetController, 'create'])
        ->name('password.reset');
});

Route::middleware('auth')->group(function () use ($loginController) {

    Route::post('logout', [$loginController, 'destroy'])
        ->name('logout');
});
